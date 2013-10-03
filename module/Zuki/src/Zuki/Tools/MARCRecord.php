<?php
/**
 * MARC Record tool
 *
 * PHP version 5
 *
 * Copyright (C) Keiji Suzuki 2013.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind2
 * @package  Controller
 * @author   Keiji Suzuki <zuki.ebetsu@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace Zuki\Tools;

/**
 * Class controls VuFind administration.
 *
 * @category VuFind2
 * @package  Controller
 * @author   Keiji Suzuki <zuki.ebetsupgmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */

class MARCRecord
{
    /**
     * config
     *
     * @var config
     */
    private $config = null;

    /**
     * Solr Record
     *
     * @var mix of Solr record object or array of tags
     */
    private $record = null;

    /**
     * Constructor
     */
    public function __construct($config, $record)
    {
        $this->config = $config;
        $this->record = $record;
    }

    /**
     * get MARC html from solr record
     *
     *
     * @return String of the MARC html
     *
     * @throws MARCRecordException
     */
    public function getMarcHtml()
    {
        $marc_fields = MARCFields::getInstance();
        // $all_fields: MARCフィールド定義（全タグ）
        $all_fields = $marc_fields->all_fields();
        // $usr_fields: MARCフィールド定義（JPMARC使用タグ）
        $use_fields = $marc_fields->use_fields();

        $url = $this->config->Site->url;

        $mrecord = $this->record->getMarcRecord();
        $html = '';
        $type = $this->getType($mrecord->getLeader());
      
        // フィールド定義にあるすべてのタグについてループ
        foreach (array_keys($all_fields) as $tag) {
            // 設定済みのタグの処理
            if ($mrecord->getFields($tag) || $tag == '000') {
                $fields = array();
                if ($tag == '000') {
                    array_push($fields, $mrecord->getLeader());
                } else {
                    $fields = $mrecord->getFields($tag);
                }

                foreach ($fields as $field) {
                    $tag_subfields = array();
                    $tag_id = 'tag['.$tag.']['.mt_rand().']';
                    // コントロールフィールドの処理
                    if (!empty($all_fields[$tag]['control'])) {
                        if ($tag == '000') {
                            $value = $field;
                        } else {
                            $value = $field->getData();
                        }
                        $sf_data = array();
                        $sf_id = $tag_id.'[@-'.mt_rand().']'; 
                        $sf_data['id']       = $sf_id;
                        $sf_data['code']     = '@';
                        $sf_data['name']    = 'controla field';
                        $sf_data['repeat']   = $all_fields[$tag]['repeat'];
                        $sf_data['required'] = $all_fields[$tag]['required'];
                        $sf_data['builder']  = isset($all_fields[$tag]['builder']) ? $all_fields[$tag]['builder'] : null;
                        $sf_data['input']    = $this->createInput($tag, $sf_id, $value);
                        array_push($tag_subfields, $sf_data);
                    // データフィールドの処理
                    } else {
                        $subfields = $field->getSubfields();
                        foreach ($subfields as $subfield) {
                            $code  = $subfield->getCode();
                            $value = $subfield->getData();
                            $sf_def = $this->getSubfieldDef($tag, $code, $all_fields);
                            $sf_data = array();
                            $sf_id   = $tag_id.'['.$code.'-'.mt_rand().']';
                            $sf_data['id']       = $sf_id;
                            $sf_data['code']     = $code;
                            $sf_data['name']    = isset($sf_def['name']) ? $sf_def['name'] : ''; 
                            $sf_data['repeat']   = isset($sf_def['repeat']) ? $sf_def['repeat'] : false;
                            $sf_data['required'] = isset($sf_def['required']) ? $sf_def['required'] : false;
                            $sf_data['builder']  = null;
                            $sf_data['input']    = $this->createInput($tag, $sf_id, $value);
                            array_push($tag_subfields, $sf_data);
                        }
                    }
                    // 設定済みのタグで入力のなかったサブフィールドを表示する
                    if (!empty($all_fields[$tag]['subfields'])) {
                        foreach ($all_fields[$tag]['subfields'] as $sf_def) {
                            $code  = $sf_def['code'];
                            $value = '';
                            if ($field->getSubfield($code)) {
                                continue;
                            }
                            $sf_data = array();
                            $sf_id   = $tag_id.'['.$code.'-'.mt_rand().']';
                            $sf_data['id']       = $sf_id;
                            $sf_data['code']     = $code;
                            $sf_data['name']    = isset($sf_def['name']) ? $sf_def['name'] : ''; 
                            $sf_data['repeat']   = isset($sf_def['repeat']) ? $sf_def['repeat'] : false;
                            $sf_data['required'] = isset($sf_def['required']) ? $sf_def['required'] : false;
                            $sf_data['builder']  = null;
                            $sf_data['input']    = $this->createInput($tag, $sf_id, '');
                            array_push($tag_subfields, $sf_data);
                        }
                    }
                    $ind = $this->getIndicator($tag, $tag_id, $field, $all_fields);
                    $html .= $this->make_html($tag, $tag_id, $ind, $tag_subfields, $all_fields, false);
                }
            // 設定されていなかったフィールドの処理
            } else {
                // JPMARCで使用されているフィールドのみ表示
                if (in_array($tag, $use_fields)) {
                    if (isset($all_fields[$tag]['type'])) {
                        $type_def = $all_fields[$tag]['type']; 
                        if (is_array($type_def)) {
                            if (!in_array($type, $type_def)) {
                                continue;
                            }
                        } else {
                            if ($type != $type_def) {
                                continue;
                            }
                        }
                    }
   
                    $tag_subfields = array();
                    $tag_id = 'tag['.$tag.']['.mt_rand().']';
                    if (empty($all_fields[$tag]['subfields'])) {
                        $sf_data = array();
                        $code    = '@';
                        $sf_id   = $tag_id.'['.$code.'-'.mt_rand().']';
                        $sf_data['id']       = $sf_id;
                        $sf_data['code']     = $code;
                        $sf_data['name']     = 'control field'; 
                        $sf_data['repeat']   = false;
                        $sf_data['required'] = null;
                        $sf_data['builder']  = isset($all_fields[$tag]['builder']) ? $all_fields[$tag]['builder'] : null;
                        $sf_data['input']    = $this->createInput($tag, $sf_id, '');
                        array_push($tag_subfields, $sf_data);
                    } else {
                        foreach ($all_fields[$tag]['subfields'] as $sf_def) {
                            $sf_data = array();
                            $code    = $sf_def['code'];
                            $sf_id   = $tag_id.'['.$code.'-'.mt_rand().']';
                            $sf_data['id']       = $sf_id;
                            $sf_data['code']     = $code;
                            $sf_data['name']    = isset($sf_def['name']) ? $sf_def['name'] : ''; 
                            $sf_data['repeat']   = isset($sf_def['repeat']) ? $sf_def['repeat'] : false;
                            $sf_data['required'] = isset($sf_def['required']) ? $sf_def['required'] : false;
                            $sf_data['builder']  = null;
                            $sf_data['input']    = $this->createInput($tag, $sf_id, '');
                            array_push($tag_subfields, $sf_data);
                        }
                    }
                    $ind = $this->getIndicator($tag, $tag_id, null, $all_fields);
                    $html .= $this->make_html($tag, $tag_id, $ind, $tag_subfields, $all_fields, true);
                }
            }
        }
        return $html;
    }

    private function getType($leader) 
    {
        $type = 'book';
        $c6 = substr($leader, 6, 1);
        switch(substr($leader, 6, 1)) {
            case 'a':
            case 't':
                if (substr($leader, 7, 1) != 'm') {
                    $type = 'serial';
                }
                break;
            case 'e':
                $type = 'map';
                break;
            case 'i':
            case 'j':
                $type = 'audio';
                break;
            case 'g':
                $type = 'visual';
                break;
            case 'm':
                $type = 'electronic';
                break;
        }
        return $type;
    }

    private function getSubfieldDef($tag, $code, $defs) 
    {
        if (empty($defs[$tag]['subfields'])) {
            return array();
        }
 
        foreach ($defs[$tag]['subfields'] as $sf_def) {
            if ($sf_def['code'] == $code) {
                return $sf_def;
            } 
        }
    
        return array();
    }

    private function createInput($tag, $sf_id, $value) 
    {
        if (strlen($value) > 100 || ($tag >= '500' && $tag < '600')) {
            return "<textarea cols='70' rows='4' id='$sf_id' name='$sf_id' class='input_marceditor' tabindex='1'>$value</textarea>";
        } else {
            return "<input type='text' id='$sf_id' name='$sf_id' tabindex='1' class='input_marceditor' value='$value' size='67' maxlength='9999'/>";
        }
    }

    private function getIndicator($tag, $tag_id, $field, $defs) 
    {
        if (!empty($defs[$tag]['control'])) {
            return '';
        }

        $ind1_id = $tag_id.'[ind1]'; 
        $ind2_id = $tag_id.'[ind2]';
        // 設定済みのフィールドの場合
        if ($field) {
            $ind1_val = $field->getIndicator(1) === false ? '' : $field->getIndicator(1);
            $ind2_val = $field->getIndicator(2) === false ? '' : $field->getIndicator(2);
        // 設定されていなかったフィールドの場合
        } else {
            $ind1_val = $this->getIndValue($defs[$tag]['ind1']);
            $ind2_val = $this->getIndValue($defs[$tag]['ind2']);
        }
        if ($ind1_val === " " || $ind1_val === "#") {
            $ind1_val =  "";
        }
        if ($ind2_val === " " || $ind2_val === "#") {
            $ind2_val =  "";
        }

        return "<input tabindex='1' class='indicator flat' type='text' name='$ind1_id' id='$ind1_id' size='1' maxlength='1' value='$ind1_val' />\n" 
             . "<input tabindex='1' class='indicator flat' type='text' name='$ind2_id' id='$ind2_id' size='1' maxlength='1' value='$ind2_val' />";
    } 
    
    private function getIndValue($val) 
    {
        if ($val) {
            return is_array($val) ? $val[0] : $val;
        } else {
            return '';
        }
    }
        
    private function make_html($tag, $tag_id, $ind, $subfields, $defs, $hidden) 
    {
        global $configArray;
        
        $url = $this->config->Site->url;
        $repeat_img  = "<img src='$url/themes/zuki/images/clone.png' class='repeat_img'/>";
        $delete_img  = "<img src='$url/themes/zuki/images/delete.png' class='delete_img'/>";
        $builder_img = "<img src='$url/themes/blueprint/images/silk/application_add.png' class='builder_img'/>";
        $expand_elm= "<a href='#' tabindex='1' class='expandfield' title='Click to Expand this Tag'>{$defs[$tag]['name']}</a>";
        $repeat_elm  = empty($defs[$tag]['repeat']) ? "" : "<a href='#' tabindex='1' class='clonetag' title='Repeat this Tag'>{$repeat_img}</a>";
        $delete_elm = empty($defs[$tag]['required']) ? "<a href='#' tabindex='1' class='deletetag' title='Delete this Tag'>{$delete_img}</a>" : "";
        $class_cf   = empty($defs[$tag]['control']) ? "" : " f{$tag}";
        $marcdoc = "<a class='marcdocs' title='link field definition'>&nbsp;?&nbsp;</a>";

        $html = <<<"EOT1"

      <div class='mtag' id='{$tag_id}'>
        <div class='tag_title' id='div_indicator_{$tag_id}'>
          <span class='tagnum' title='{$defs[$tag]['name']}'>{$tag}</span>
          {$marcdoc}&nbsp;{$ind}
          &nbsp;-&nbsp; {$expand_elm}
          <span class='subfield_controls'>
              {$repeat_elm} {$delete_elm}
          </span>
        </div><!-- tag_title -->
EOT1;

        foreach ($subfields as $subfield) {
            $sf_id         = $subfield['id'];
            $sf_code       = $subfield['code'];
            $sf_name       = $subfield['name'];
            $sf_repeat     = $subfield['repeat'];
            $sf_required   = $subfield['required'];
            $sf_builder    = $subfield['builder'];
            $sf_input      = $subfield['input'];

            $sf_div_id     = $sf_id . "[line]";
            $dsp_style     = $hidden ? "display: none; " : "";
            $sf_up_elm = "<img src='{$url}/themes/zuki/images/up.png' class='upsubfield' alt='Move Up' title='Move Up'/>";
            $sf_repeat_elm  = $sf_repeat ? "<a href='#' tabindex='1' class='clonesubfield' title='Repeat this Tag'>{$repeat_img}</a>" : "";
            $sf_delete_elm  = $sf_repeat ? "<a href='#' class='deletesubfield' tabindex='1'>{$delete_img}</a>" : "" ;
            $sf_builder_elm = $sf_builder ? "<a href='$url/$sf_builder' title='subfield builder' class='sf_builder_{$tag}'>$builder_img</a>" : "";

            $html .= <<<"EOT2"

        <div class='subfield_line{$class_cf}' style='{$dsp_style}float: left; clear: left; width: 100%;' id='{$sf_id}[line]'>
          <label for='{$sf_id}' class="labelsubfield">
            <span class='subfieldcode'>
              {$sf_up_elm} &nbsp;{$sf_code} - {$sf_name}
            </span>
          </label> 
      {$sf_input}
          <span class='subfield_controls'>
              {$sf_builder_elm} {$sf_repeat_elm} {$sf_delete_elm}
          </span> 
        </div><!-- subfield_line -->
EOT2;
        }
        $html .= <<<"EOT3"

      </div><!-- mtag -->
EOT3;
        return $html;
    }
}
