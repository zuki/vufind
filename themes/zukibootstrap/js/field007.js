(function($) {
    var result;
    $(document).ready(function() {
        result = $('#result007');
        // 資料種別の設定
        var type = get007Type(result.val());
        $('#F007_TYPE_SELECT_VAL').val(type);
        changeTypeofMaterial007(type);

        // 資料種別変更時の再描画の設定
        $('#F007_TYPE_SELECT_VAL').change(function() {
            changeTypeofMaterial007($(this).val());
        });

        $('#btn_submit').click(function() {
            hideLightbox();
            var subfield = $('.sf_builder_007').closest('div[class="subfield_line f007"]');
            var sf_val = result.val();
            //console.log("sf_val: "+sf_val);
            $(':input.input_marceditor', subfield).val(sf_val);
            return false;
        });

        $('#btn_cancel').click(function() {
            hideLightbox();
            return false;
        });

    });

    // 指定した資料種別用のデータ入力テーブルを作成する
    function changeTypeofMaterial007(type) {
        var id = '#F007_' + type;
        $('#F007')
           .children()
           .remove()
           .end()
           .append($(id).find('tbody').children().clone(false));
           
        // 新007フィールドの00のセットとresult長の調整
        var first_val = $('#F007').find('tr').first().find('select').first().val();
        var last_pos = getPositionFromID($('#F007').find('tr').last().attr('id')+'_VAL');
        var index;
        if ((index = last_pos.indexOf("-")) > 0) {
            last_pos = parseInt(last_pos.substr(index + 1) ,10);
        } else {
            last_pos =  parseInt(last_pos, 10);
        }
        var result_val = result.val();
        result_val = first_val + result_val.substr(1);
        if(last_pos > result_val.length) {
            for (var i=0; i < (last_pos - result_val.length + 1) ; i++) result_val += "#";
        } else {
            result_val = result_val.substr(0, last_pos+1);
        }
        
        result.val(result_val);
        setValueOfElements($('#F007'), result_val);
        //console.log('result = '+$('#result007').val());

        $('#F007').find('input, select').each(function() {
             $(this).change(function() {
                var pos = getPositionFromID($(this).attr('id'));
                result_val = changePosResult(pos, $(this).val(), result_val);
                result.val(result_val);
                //console.log('result = '+$('#result007').val());
            });
        });
    }

    function get007Type(value) {
        var type;
        switch(value.charAt(0)) {
            case 'a':
                type = 'MAP';
                break;
            case 'c':
                type = 'CMP';
                break;
            case 'd':
                type = 'GLB';
                break;
            case 'f':
                type = 'TAC';
                break;
            case 'g':
                type = 'PRG';
                break;
            case 'h':
                type = 'MIC';
                break;
            case 'k':
                type = 'GRF';
                break;
            case 'm':
                type = 'CMP';
                break;
            case 'o':
                type = 'KIT';
                break;
            case 'q':
                type = 'NOM';
                break;
            case 'r':
                type = 'RIS';
                break;
            case 's':
                type = 'REC';
                break;
            case 't':
                type = 'TXT';
                break;
            case 'v':
                type = 'VDO';
                break;
            case 'z':
            default:
                type = 'UNS';
        }
        return type;
    }
    
})(jQuery);
