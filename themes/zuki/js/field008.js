(function($) {
    var result, tr_result, h4_result;
    $(document).ready(function() {
        // グローバル変数のセット
        result = $('#result');
        tr_result = $('#tr_result');
        h4_result = $('#h4_result');

        // 資料種別の設定
        var type = get008Type($('#leader').val());
        $('#F008_TYPE_SELECT_VAL').val(type);
        changeTypeofMaterial(type);

        // 資料種別変更時の再描画の設定
        $('#F008_TYPE_SELECT_VAL').change(function() {
            changeTypeofMaterial($(this).val());
        });

        $('#btn_submit').click(function() {
            hideLightbox();
            var subfield = $('.sf_builder_008').closest('div[class="subfield_line f008"]');
            var sf_val = result.val();
            //console.log("saved sf_val: "+sf_val);
            $(':input.input_marceditor', subfield).val(sf_val);
            return false;
        });

        $('#btn_cancel').click(function() {
            hideLightbox();
            return false;
        });

    });

    // 指定した資料種別用のデータ入力テーブルを作成する
    function changeTypeofMaterial(type) {
        var id = '#F008_' + type;
        var result_val = result.val();
        $('#F008')
          .find('.variable')
          .each(function() {
              $(this).children().remove();
           })
          .end()
          .find('tr')
          .eq(4)
          .after($(id).find('tbody')
                 .children()
                 .clone(false)
                );
        setValueOfElements($('#F008'), result_val);
        renderResult(tr_result, result_val, true);
        $('#F008')
            .find('input, select')
            .each(function() {
                $(this).change(function() {
                    var changed = $(this);
                    var pos = getPositionFromID(changed.attr('id'));
                    var new_val = changePosResult(pos, changed.val(), result.val());
                    renderResult(tr_result, new_val, pos);
                    result.val(new_val);
                });
            });
    }
    
    // 008用の資料種別をリーダーから判定する
    function get008Type(leader) {
        var type;
        switch(leader.charAt(6)) {
            case 'a':
            case 't':
                if (leader.charAt(7) == 'm') {
                    type = 'BKS';
                } else {
                    type = 'CNT';
                }
                break;
            case 'c':
            case 'i':
            case 'j':
                type = 'MUS';
                break;
            case 'e':
                type = 'MAP';
                break;
            case 'g':
            case 'k':
                type = 'VIS';
                break;
            case 'm':
                type = 'CMP';
                break;
            default:
                type = 'MIX';
        }
        return type;
    }

    // 指定された値をtr_result に反映する（設定した値と不正な値をハイライトする）
    function renderResult(tr_result, result)
    {
        var i, j, td, value, escaped_value, render, pos, obj,
            ini = -1,
            end = -1,
            args = renderResult.arguments,
            whiteAllTD = false,
            found = false;
            
        if (tr_result) {
            if (tr_result.find('td').length != result.length) {
                tr_result.find('td').each(function() {
                    $(this).remove();
                });
                for (i=0; i < result.length; i++) {
                    tr_result.append($('<td>'));
                }
            }
            if (args.length > 2) {
                if (typeof(args[2]) == "boolean") {
                    whiteAllTD = args[2];
                } else {
                    var index;
                    if ((index = args[2].indexOf("-")) > 0) {
                        ini = parseInt(args[2].substring(0, index) ,10);
                        end = parseInt(args[2].substr(index + 1) ,10);
                    } else {
                        ini = parseInt(args[2], 10);
                    }
                }
            }
            
            for (i=0; i < result.length; i++) {
                value = result.charAt(i);
                td = tr_result.find('td').eq(i);
                if (td.css('backgroundColor') != 'yellow' || whiteAllTD) {
                    td.css('backgroundColor', 'white');
                }
                escaped_value = (value == ' ' ? '&nbsp;' : value);
                td.html(escaped_value);
                //console.log('td['+i+'] = '+td.html());
                td.attr('title', 'Pos ' + i + '. Value: \"' + value + '\"');
                td.attr('class', 'f008_val');
                if (ini >= 0) {
                    if (end > 0) {
                        if (ini <= i && i <= end) td.css('backgroundColor', '#cccccc');
                    } else if (i == ini) td.css('backgroundColor', '#cccccc');
                }
            }
        }
    }

})(jQuery);
