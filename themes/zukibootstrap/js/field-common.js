    // 指定フィールドの位置情報をフィールド定義から求める
    function getPositionFromID(id) {
        var parts = id.split('_');
        if (parts.length == 4) {
            return parts[2].substr(1);
        } else if (parts.length == 5) {
            return parts[2].substr(1) + '-' + parts[3];
        } else {
            return null;
        }
    }//getPositionFromID

    // 各フィールドに値を設定する
    function setValueOfElements(form, result) {
        form.find('input, select').each(function() {
            var elm = $(this);
            var pos = getPositionFromID(elm.attr('id'));
            var value = returnValuePosFromResult(result, pos);
            value = value.replace(/ /g, "#");
            elm.val(value);
        });
    }

    // result値上の指定された位置の値を返す
    function returnValuePosFromResult(result, pos) {
        var index, ini, end;
        if ((index = pos.indexOf("-")) > 0) {
            ini = parseInt(pos.substring(0, index) ,10);
            end = parseInt(pos.substr(index + 1) ,10);
            return result.substring(ini, end + 1);
        } else {
            return result.substr(pos, 1);
        }
    }

    // result値上の指定された位置の値を指定した値に変更して変更されたresultを返す
    function changePosResult(pos, value, resultStr)
    {
        var index, ini, end, roffset;
        var result = "";
        if ((index = pos.indexOf("-")) > 0) {
            ini = parseInt(pos.substring(0, index) ,10);
            end = parseInt(pos.substr(index + 1) ,10);
            roffset = (1 + end - ini)- value.length;
            if (roffset > 0) for (var i=0; i < roffset; i++) value += "#";
            if (ini == 0)
                result = value + resultStr.substr(end + 1);
            else {
                result = resultStr.substring(0, ini) + value;
                if (end < resultStr.length)
                    result += resultStr.substr(end + 1);
            }
        } else {
            ini = parseInt(pos, 10);
            if (ini == 0)
                result = value + resultStr.substr(1);
            else {
                result = resultStr.substring(0, ini) + value;
                if (ini < resultStr.length)
                    result += resultStr.substr(ini + 1);
            }
        }
        result = result.replace(/#/g, " ");
        return result;
    }
