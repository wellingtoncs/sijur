/*
Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

var campos = str_retorno_ajax.split("|_|");

CKEDITOR.dialog.add( 'smiley', function( editor )
{
	var tenant_fields = []; //new Array();
	for(i=0;i<=campos.length;i++){
		strcampos = new String(campos[i]);
		var dados = strcampos.split("_|_");
		if(dados[1]!=undefined){
			tenant_fields[i]=[dados[0], dados[1]];
		}
	}
	return {
		title : 'Campos',
		minWidth : 300,
		minHeight : 100,
		contents :
		[
			{
				id : 'tab1',
				label : 'Tenants',
				elements :
				[
					{
						id : 'tenant_dropdown',
						type : 'select',
						label : 'Selecione o Campo.',
						'default':'',
						items: tenant_fields,
						onChange : function( api ) {
						  //this = CKEDITOR.ui.dialog.select
						}
					}
				]
			}
		],
		buttons : [ CKEDITOR.dialog.okButton, CKEDITOR.dialog.cancelButton ],
		onOk : function()
		{
			var textareaObj = this.getContentElement( 'tab1', 'tenant_dropdown' );
			editor.insertHtml( textareaObj.getValue());
		}
	};
} );
