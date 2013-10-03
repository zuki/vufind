(function ($) {
    var fields_in_use = {};

    $(document).ready(function() {
        // 使用タグ、サブフィールドの保存
        $('.mtag').each(function() {
            var id = get_id($(this).attr('id'), true);
            if (id in fields_in_use) {
                fields_in_use[id]++;
            } else {
                fields_in_use[id] = 1;
            } 
        });
        $('.subfield_line').each(function() {
            var id = get_id($(this).attr('id'), false);
            if (id in fields_in_use) {
                fields_in_use[id]++;
            } else {
                fields_in_use[id] = 1;
            } 
        });

        // タグの表示トグル
        $('.expandfield').click(function() {
            var target = $(this).closest('div[id^="tag"]');
            target.children('div[id^="tag"]').toggle();
            $.scrollTo(target, 500);
        });

        // サブフィールドの表示順入れ替え
        $('.upsubfield').click(function() {
            var sf  = $(this).closest('div[class="subfield_line"]'); 
            var tag = sf.parent();
            var sfs = tag.children('div');

            if (sfs.length <= 1) return; //サブフィールドが1つだけの場合は何もしない

            sfs.each(function(index) {
                if ($(this).attr('id') == sf.attr('id')) {
                    if (index == 1) {         // クリックされたサブフィールドが先頭
                        tag.append($(this));  // （index=0はインジケータ）
                        return;
                    } else {
                        $(this).prev().before(this);
                        return;
                    }
                }
            });
        });

        // タグフィールドの削除
        $('.deletetag, .deletesubfield').click(function() {
            var target = $(this).closest('div[id^="tag"]');
            var prev_elm = target.prev();
            remove_item(target, true);
            $.scrollTo(prev_elm, 500);
        });

        // サブフィールドの削除
        $('.deletesubfield').click(function() {
            var target = $(this).closest('div[id^="tag"]');
            var prev_elm = target.prev();
            remove_item(target, false);
            $.scrollTo(prev_elm, 500);
        });

        // タグの複製
        $('.clonetag').click(function() {
            var target = $(this).closest('div[class="mtag"]');
            var target_id = target.attr('id');
            fields_in_use[get_id(target_id, true)]++;

            // <div class="tag_title"> の複製
            var tag_clone = target.clone(true);
            var tag_clone_id = get_new_id(target_id, true);
            tag_clone.attr('id', tag_clone_id);
            $('div.tag_title', tag_clone).attr('id', 'div_indicator_'+tag_clone_id);
            $(':input.indicator', tag_clone).each(function(i) {
                $(this).attr('id',   target_id+'[ind'+(i+1)+']');
                $(this).attr('name', target_id+'[ind'+(i+1)+']');
            });

            // <div class="subfield_line">の複製
            var first_sf = null;
            $('div.subfield_line', tag_clone).each(function() {
                var clone_sf = $(this);
                if (!first_sf) {
                    first_sf = clone_sf;
                }
                var sf_code = get_subfield_code(clone_sf.attr('id'));
                var clone_sf_id = tag_clone_id+'['+sf_code+'-'+create_key()+']';
                clone_sf.attr('id', clone_sf_id+'[line]');
                $('.labelsubfield', clone_sf).attr('for', clone_sf_id);
                $('.input_marceditor', clone_sf).attr('id', clone_sf_id);
                $('.input_marceditor', clone_sf).attr('name', clone_sf_id);
            });
            // 複製タグを追加
            target.after(tag_clone);
            $(':input.input_marceditor', first_sf).focus().select();
            $.scrollTo(target, 500);
        });
   
        // サブフィールドの複製
        $('.clonesubfield').click(function() {
            var target = $(this).closest('div[class="subfield_line"]');
            var target_id = target.attr('id');
            var tag_id = get_id(target_id, false);
            fields_in_use[tag_id]++;

            var clone = target.clone(true);
            var clone_sf_id = get_new_id(target_id, false);
            var clone_sf_field_id = clone_sf_id.replace(/\[line\]/, "");
            clone.attr('id', clone_sf_id);
            $('.labelsubfield', clone).attr('for', clone_sf_field_id);
            var clone_sf_input = $('.input_marceditor', clone);
            clone_sf_input.attr('id', clone_sf_field_id);
            clone_sf_input.attr('name', clone_sf_field_id);
            target.after(clone);
            clone_sf_input.focus().select();
            $.scrollTo(target, 500);
        });

        // リーダー編集画面へのリンク
        $('.sf_builder_000').click(function() {
            var id = 'dummy';
            var leader_val = get_field_val('f000');
            var $dialog = getLightbox('Admin', 'Leader', id, null, 'Leader', 'Admin', 'Leader', id, "leader_val="+leader_val);
            return false;
        });

        // フィールド007編集画面へのリンク
        $('.sf_builder_007').click(function() {
            var id = 'dummy';
            var field_val  = get_field_val('f007');
            var $dialog    = getLightbox('Admin', 'Field007', id, null, 'Field007', 'Admin', 'Field007', id, "field_val="+field_val);
            return false;
        });

        // フィールド008編集画面へのリンク
        $('.sf_builder_008').click(function() {
            var id = 'dummy';
            var leader_val = get_field_val('f000');
            var field_val  = get_field_val('f008');
            var $dialog    = getLightbox('Admin', 'Field008', id, null, 'Field008', 'Admin', 'Field008', id, "leader_val="+leader_val+"&field_val="+field_val);
            return false;
        });

        // フィールド定義ページ（LOC）へのリンク
        $('.marcdocs').click(function() {
            var tag = $(this).prev().text();
            popup_marc_doc(tag);
            return false;
        });

    });

    function get_field_val(field) {
        var field_elm = $('div[class="subfield_line '+field+'"]');
        return $(':input.input_marceditor', field_elm).val();
    }

    function get_id(id, istag) {
        if (istag) {
            return 'tag'+id.substring(4, 7);
        } else {
            var tag = id.substring(4, 7);
            var code = get_subfield_code(id);
            return 'sf'+tag+code;
        }
    }

    function get_new_id(id, istag) {
        var parts = id.split("]");
        if (istag) {
            parts[1] = '[' + create_key();
        } else {
            var code = get_subfield_code(id); 
            parts[2] = "[" + code + '-' + create_key();
        }
        return parts.join("]"); 
    }

    function get_subfield_code(id) {
        var parts = id.split("]");
        var pos = parts[2].indexOf('-');
        return parts[2].substring(1, pos); 
    }
        
    function remove_item(target, istag) {
        var id = get_id(target.attr('id'), istag);
        if (1 == fields_in_use[id]) {
            $(":input.input_marceditor", target).each(function() {
                $(this).val("");
            });
        } else {
            target.remove();
            fields_in_use[id]--;
        }
    }

    function create_key() {
        return parseInt(Math.random() * 100000);
    }

    function popup_marc_doc(field) {
        if (field == 0) {
            window.open("http://www.loc.gov/marc/bibliographic/bdleader.html");
        } else if (field < 900) {
            window.open("http://www.loc.gov/marc/bibliographic/bd" + ("000"+field).slice(-3) + ".html");
        } else {
            window.open("http://www.loc.gov/marc/bibliographic/bd9xx.html");
        }
    }
        

})(jQuery);

