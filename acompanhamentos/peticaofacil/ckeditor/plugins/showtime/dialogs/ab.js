/*********************************************************************************************************/
/**
 * showtime plugin for CKEditor 3.x (Author: Arvind Bhardwaj ; www.webspeaks.in)
 * CKEditor 3.x showtime JS plugin
 * version:	 1.0
 */
/*********************************************************************************************************/

CKEDITOR.dialog.add("showtime",function(e){

	var date=new Date();
	var months = new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

	var h=date.getHours();
	var nh=h;
	var m=date.getMinutes();
	var s=date.getSeconds();
	var a;
	if(h>12){a="PM"; nh=h-12;}
	if(h<=12){a="AM"; nh=h;}
	
	var t1 = months[parseInt(date.getMonth())]+' '+date.getDate()+" @ " + nh +  ":" + m + " " + a;
	var t2 = date.getFullYear()+'-'+parseInt(date.getMonth()+1) +'-'+date.getDate()+' '+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
	var t3 = date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();

	return {
		title:'Show Time',
		resizable : CKEDITOR.DIALOG_RESIZE_BOTH,
		minWidth:300,
		minHeight:100,
		onShow:function(){ 
		},
		onLoad:function(){ 
				dialog = this; 
				this.setupContent();
		},
		onOk:function(){
		},
		contents:[
		{		id:"info",
				name:'info',
				label:'Tab',
				elements:[

					{
						id : 'format',
						type : 'select',
						label : 'Format',
						accessKey : 'T',
						items :
						[
							[ t1 ],
							[ t2 ],
							[ t3 ]
						]
					},
					{  
						type:'html',
						html:'<span style="">'+'Select the date format'+'</span>'
					}
				]
		}
		],
		buttons:[{
			type:'button',
			id:'okBtn',
			label: 'Set',
			onClick: function(){
			   addCode();
			}
		}, CKEDITOR.dialog.cancelButton],
};

	function addCode(){

		var t = dialog.getValueOf('info', 'format');
		if(t.length == 0){
			alert('Please select date format.')
			return false;
		}

		var str= "";
		str = e.getData();

		var myEditor = CKEDITOR.instances.editor1;

		myEditor.insertHtml(t);

		return false;

	};


});

