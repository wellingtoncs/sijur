<?php

// Execute this file only once
if (defined('_INC_OBJ_EXP_AC')) return;
define('_INC_OBJ_EXP_AC', '1');

class LcmExpenseAccess {
	var $allow;
	var $users;

	var $pub_read;
	var $pub_write;

	function LcmExpenseAccess($id_expense, $id_case = 0, $obj_exp = null) {
		// Basic rights
		$this->users = array();
		$this->allow = array('r' => false, 'w' => false, 'e' => false, 'a' => false);

		// If attached to case (or trying to attach), check case AC
		if ($id_case) {
			$case_ac = new LcmCaseAccess($this->getDataInt('id_case'));
			lcm_panic("TODO");
		}

		if (! $obj_exp) 
			$obj_exp = new LcmExpense($id_expense, $id_case);

		$this->pub_read = $obj_exp->getDataInt('pub_read');
		$this->pub_write = $obj_exp->getDataInt('pub_write');

		//
		// Permissions for the creator of the request
		//
		$p = array('r' => true, 'a' => false);

		if($obj_exp->getDataString('status') == 'pending') {
			$p['e'] = true;
			$p['w'] = true;
		}

		$this->users[$obj_exp->getDataInt('id_author')] = $p;
	}

	function getRead() {
		global $author_session;

		if ($author_session['status'] == 'admin')
			return true;

		// TODO: Check case AC

		if ($this->pub_read)
			return true;

		if ($this->users[$author_session['id_author']]['r'])
			return true;

		return false;
	}

	function getAdd() {
		global $author_session;

		if ($author_session['status'] == 'admin')
			return true;

		// TODO: Check case AC

		if ($this->pub_read)
			return true;

		if ($this->users[$author_session['id_author']]['w'])
			return true;

		return false;
	}

	function getEdit() {
		global $author_session;

		if ($author_session['status'] == 'admin')
			return true;

		// TODO: Check case AC

		if ($this->users[$author_session['id_author']]['e'])
			return true;

		return false;
	}

	function getAdmin() {
		global $author_session;

		if ($author_session['status'] == 'admin' || $author_session['status'] == 'manager')
			return true;

		return false;
	}
}

class LcmExpenseCommentAccess {
	var $id_author = 0;
	var $exp_status = '';

	function LcmExpenseCommentAccess($id_comment, $obj_comment = null) {
		if ($obj_comment)
			$id_comment = $obj_comment->getDataInt('id_comment');
		else
			$obj_comment = new LcmExpenseComment($id_comment);

		lcm_log("id_comment = $id_comment");

		if ($id_comment) {
			$query = "SELECT status 
					FROM lcm_expense 
					WHERE id_expense = " . $obj_comment->getDataInt('id_expense');

			$result = lcm_query($query);

			if (($row = lcm_fetch_array($result)))
				$this->exp_status = $row['status'];
			else
				lcm_panic("Had id_comment but no associated expense?");
		}

		$this->id_author = $obj_comment->getDataInt('id_author');
	}

	function getRead() {
		// Note: it is the responsability of expense->printComments()
		// to check for access rights. There is no AC on individual
		// comments.

		return true;
	}

	function getAdd() {
		lcm_panic("Cannot call getAdd() on a comment. Perhaps you meant getEdit() ?");
	}

	function getEdit() {
		global $author_session;

		if ($author_session['status'] == 'admin')
			return true;

		if ($this->exp_status == 'pending')
			if ($author_session['id_author'] == $this->id_author)
				return true;

		return false;
	}

	function getAdmin() {
		lcm_panic("Cannot call getAdmin() on a comment");
	}
}

?>
