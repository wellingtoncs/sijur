//jFilterXCel2003
//Author: Gabriel Gois de Melo
//http://gabrielgoismelo.wordpress.com
//V 1.0.1
//Licence: GNU

/*
Exemple avaliable (portuguese) at http://gabrielgoismelo.wordpress.com/2012/06/04/jquery-filtro-tabela-excel-2003/
*/

function carregarFiltros(idTabela) {
	
    var tabela = $("#" + idTabela);
	var classeDeterminanteFiltro = "comFiltro";

    //pegar primeira linha (cabe�alho)
    var cabecalho = $(tabela).find("tr:first");
    var colunasComFiltro = new Array();

    //determinar quais colunas tem filtro
    $(cabecalho).find("th").each(function () {
        colunasComFiltro.push($(this).hasClass(classeDeterminanteFiltro));
    });

    var line = 0;
    var col = 0;
    var maxCols = $(cabecalho).find("th").size();

    //para cada uma que tiver filtros
    for(var i = 0; i < colunasComFiltro.length; i++) {
        //se n�o tem filtro, next!!!
        if (colunasComFiltro[i] == false)
            continue;

        var vetorItens = new Array();
        var idColuna = idTabela + "_col_" + i;

        //pegar cada linha
        $(tabela).find("tr").each(function () {

            //descobrir o elemento na posi��o i
            var item = $(this).find("td").eq(i).html();

            if (item != null) {

                //comparar ele com a lista existente
                var existe = searchArray(item, vetorItens, false);

                //se n�o existir, inserir e ordenar
                if (existe == -1) {
                    vetorItens.push(item);

                    //ordenar
                    vetorItens.sort();
                }
            }
        });


        //findado, geremos o select
        var saida = "<select id=\"" + idColuna + "\" col=\""+i+"\" style=\"max-width:120px;color:blue;font-weight:bold; font-family:arial; font-size:9pt\">\n";

        //pegar o valor da coluna
        saida += "\t<option value=\"\">" + $(cabecalho).find("th").eq(i).html() + "</option>\n";

        //cada valor especifico
        for (var j = 0; j < vetorItens.length; j++) {
            saida += "\t<option value=\"" + vetorItens[j] + "\">" + vetorItens[j] + "</option>\n";
        }

        saida += "</select>";

        $(cabecalho).find("th").eq(i).html(saida);

        //inserir os bindings
        $("#" + idColuna).change(function () {
            filtrarColuna(idTabela);
        });

    }
}

function filtrarColuna(idTabela) {
    var objTabela = $("#" + idTabela);
	var cls=0;
	var crs=0;
	var nrs=0;
    //zerar tabela
    $(objTabela).find("tr").css("display", "");

    //pegar cabecalho
    var cabecalho = $(objTabela).find("tr").first();

    var i = 0;

    //pegar cada filtro
    $(cabecalho).find("th").each(function () {

        //verificar se tem um select dentro dele
        var selecter = $(this).find("select");

		//tem a lista de sele��o
        if (selecter != null && selecter.toString() != "undefined" && $(selecter).val() != undefined && $(selecter).val() != "") {

			//pegar o valor
            var valorSelecter = $(selecter).val();

			//se tiver valor, executar a busca
            if (valorSelecter != "") {
				//para cada linha da tabela
                $(objTabela).find("tr").each(function () {
					//eq = seleciona item o indice i (zerobased)
                    if ($(this).find("td").eq(i).html() != valorSelecter)
                        $(this).css("display", "none");
                });
            }
        }
        i++;
    });
	
	$('.cls_td:visible').each(function(index,object) {
		cls++;
	});
	if ($('#id_crs:visible').is(":visible") == true) {
		$('.cls_rs:visible').each(function(index,object) {
			nrs = $(this).html().replace(".","").replace(",",".");
			crs = parseFloat(nrs) + crs;
		});
		$("#id_crs").html("Valor Total: <span style='color:yellow'><b> R$ " + crs.formatMoney(2,',','.') + "</b></span>");		
	}
	$("#id_sel").html("<i>Total selecionado: "+cls+"</i>");
	//reexibir o cabe�alho
    $(objTabela).find("tr").first().css("display", "");
	//total_tr(idTabela);
}

//busca bin�ria - esse c�digo n�o � de minha autoria.
//author: Sean Murphy
//http://snippets.dzone.com/posts/show/5989
searchArray = function (needle, haystack, case_insensitive) {
    if (typeof (haystack) === 'undefined' || !haystack.length) return -1;

    var high = haystack.length - 1;
    var low = 0;
    case_insensitive = (typeof (case_insensitive) === 'undefined' || case_insensitive) ? true : false;
    needle = (case_insensitive) ? needle.toLowerCase() : needle;

    while (low <= high) {
        mid = parseInt((low + high) / 2)
        element = (case_insensitive) ? haystack[mid].toLowerCase() : haystack[mid];
        if (element > needle) {
            high = mid - 1;
        } else if (element < needle) {
            low = mid + 1;
        } else {
            return mid;
        }
    }
    return -1;
};

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };