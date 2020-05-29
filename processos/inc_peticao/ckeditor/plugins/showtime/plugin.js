/*********************************************************************************************************/
/**
 * showtime plugin for CKEditor 3.x (Author: Arvind Bhardwaj ; www.webspeaks.in)
 * CKEditor 3.x showtime JS plugin
 * version:	 1.0
 */
/*********************************************************************************************************/

CKEDITOR.plugins.add('showtime',   
{    
    requires: ['dialog'],
    init:function(a) {
		var b="showtime";
		var c=a.addCommand(b,new CKEDITOR.dialogCommand(b));
		c.modes={wysiwyg:1,source:1};	//Enable our plugin in bothe modes
		c.canUndo=true;

		a.ui.addButton("showtime",
		{
			label:'Show current time',
			command:b,
			icon:this.path+"showtime.png"
		});
		CKEDITOR.dialog.add(b,this.path+"dialogs/ab.js")
	}
});