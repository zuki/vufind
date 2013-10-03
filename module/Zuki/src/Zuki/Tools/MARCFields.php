<?php
/**
 * MARC Fields
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
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org   Main Site
 */
namespace Zuki\Tools;
	
class MARCFields
{
	private static $fields_json = '{
		"000" : {
	 		"name"     : "リーダー",
			"required" : true,
			"repeat"   : false,
			"builder"  : "Admin/Leader",
			"control"  : true,
			"default"  : "     cam a2200000zi 4500"
		},
		"001" : {
			"name"     : "管理番号",
			"required" : true,
			"repeat"   : false,
			"control"  : true,
			"default"  : null
		},
		"003" : {
			"name"     : "管理番号識別子",
			"required" : true,
			"repeat"   : false,
			"control"  : true,
			"default"  : "JTNDL"
		},
		"005" : {
			"name"     : "最終更新年月日",
			"required" : true,
			"repeat"   : false,
			"control"  : true,
			"default"  : null
		},
		"006" : {
			"name"     : "付加的資料属性",
			"required" : false,
			"repeat"   : false,
			"control"  : true,
			"default"  : null
		},
		"007" : {
			"name"     : "物理的属性コード",
			"required" : true,
			"repeat"   : false,
			"builder"  : "Admin/Field007",
			"control"  : true,
			"default"  : "ta"
		},
		"008" : {
			"name"     : "一般コード化情報",
			"required" : true,
			"repeat"   : false,
			"builder"  : "Admin/Field008",
			"control"  : true,
			"default"  : "######s2012####ja#||||g#||||#||||||jpn##"
		},
		"010" : {
			"name"      : "Library of Congress Control Number",
			"subfields" : null
		},
		"013" : {
			"name"      : "Patent Control Information",
			"subfields" : null
		},
		"015" : {
			"name"      : "全国書誌番号",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "全国書誌番号",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "全国書誌作成機関",
					"required"  : true,
					"repeat"    : false,
					"default"   : "jnb"
				}
			]
		},
		"016" : {
			"name"      : "National Bibliographic Agency Control Number",
			"subfields" : null
		},
		"017" : {
			"name"      : "Copyright or Legal Deposit Number",
			"subfields" : null
		},
		"018" : {
			"name"      : "Copyright Article-Fee Code",
			"subfields" : null
		},
		"020" : {
			"name"      : "ISBN",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "ISBN",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "入手条件・定価",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "取消・無効ISBN",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"022" : {
			"name"      : "ISSN",
			"ind1"      : [" ", "0"],
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "ISBN",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "l",
					"name"      : "ISSN-L",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "m",
					"name"      : "取消ISSN-L",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "y",
					"name"      : "誤りISSN",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "取消ISSN",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "ISSNセンターコード",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"024" : {
			"name"      : "Other Standard Identifier",
			"subfields" : null
		},
		"025" : {
			"name"      : "Overseas Acquisition Number",
			"subfields" : null
		},
		"026" : {
			"name"      : "Fingerprint Identifier",
			"subfields" : null
		},
		"027" : {
			"name"      : "Standard Technical Report Number",
			"subfields" : null
		},
		"028" : {
			"name"      : "出版者番号",
			"ind1"      : ["0", "1", "2", "5"],
			"ind2"      : "0",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "出版者番号",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "レーベル名",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"030" : {
			"name"      : "CODEN Designation",
			"subfields" : null
		},
		"031" : {
			"name"      : "Musical Incipits Information",
			"subfields" : null
		},
		"032" : {
			"name"      : "Postal Registration Number",
			"subfields" : null
		},
		"033" : {
			"name"      : "Date/Time and Place of an Event",
			"subfields" : null
		},
		"034" : {
			"name"      : "数値データ：コード化情報（地図資料）",
			"ind1"      : ["0", "1", "3"],
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "map",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "縮尺種別",
					"required"  : true,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "水平縮尺",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "垂直縮尺",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "d",
					"name"      : "最西経度",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "e",
					"name"      : "最東経度",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "f",
					"name"      : "最北緯度",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "g",
					"name"      : "最南緯度",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"035" : {
			"name"      : "他MARC番号等",
			"ind1"      : [" ", "9"],
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "他MARC番号等",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "無効な他MARC番号等",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"036" : {
			"name"      : "Original Study Number for Computer Data Files",
			"subfields" : null
		},
		"037" : {
			"name"      : "Source of Acquisition ",
			"subfields" : null
		},
		"038" : {
			"name"      : "Record Content Licensor",
			"subfields" : null
		},
		"040" : {
			"name"      : "レコード作成機関",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : true,
			"repeat"    : false,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "作成機関コード",
					"required"  : false,
					"repeat"    : false,
					"default"   : "JTNDL"
				},
				{
					"code"      : "b",
					"name"      : "目録用言語コード",
					"required"  : false,
					"repeat"    : false,
					"default"   : "jpn"
				},
				{
					"code"      : "c",
					"name"      : "レコード変換機関",
					"required"  : true,
					"repeat"    : false,
					"default"   : "JTNDL"
				},
				{
					"code"      : "e",
					"name"      : "目録規則",
					"required"  : false,
					"repeat"    : true,
					"default"   : "ncr/1987"
				}
			]
		},
		"041" : {
			"name"      : "言語コード",
			"ind1"      : ["0", "1"],
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "本文",
					"required"  : true,
					"repeat"    : true,
					"default"   : "jpn"
				},
				{
					"code"      : "h",
					"name"      : "原文",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"042" : {
			"name"      : "Authentication Code",
			"subfields" : null
		},
		"043" : {
			"name"      : "Geographic Area code",
			"subfields" : null
		},
		"044" : {
			"name"      : "出版・製作国コード",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "MARC国名コード",
					"required"  : true,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"045" : {
			"name"      : "内容年",
			"ind1"      : ["0", "1", "2"],
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"subfields" : [
				{
					"code"      : "b",
					"name"      : "西暦年",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"046" : {
			"name"      : "Special Coded Dates",
			"subfields" : null
		},
		"047" : {
			"name"      : "Form of Musical Composition Code",
			"subfields" : null
		},
		"048" : {
			"name"      : "Number of Musical Instruments or Voices Codes",
			"subfields" : null
		},
		"050" : {
			"name"      : "Library of Congress Call Number",
			"subfields" : null
		},
		"051" : {
			"name"      : "Library of Congress Copy, Issue, Offprint Statement",
			"subfields" : null
		},
		"052" : {
			"name"      : "Geographic Classification",
			"subfields" : null
		},
		"055" : {
			"name"      : "Classification Numbers Assigned in Canada",
			"subfields" : null
		},
		"060" : {
			"name"      : "National Library of Medicine Call Number",
			"subfields" : null
		},
		"061" : {
			"name"      : "National Library of Medicine Copy Statement",
			"subfields" : null
		},
		"066" : {
			"name"      : "Character Sets Present",
			"subfields" : null
		},
		"070" : {
			"name"      : "National Agricultural Library Call Number",
			"subfields" : null
		},
		"071" : {
			"name"      : "National Agricultural Library Copy Statement",
			"subfields" : null
		},
		"072" : {
			"name"      : "Subject Category Code",
			"subfields" : null
		},
		"074" : {
			"name"      : "GPO Item Number",
			"subfields" : null
		},
		"080" : {
			"name"      : "Universal Decimal Classification Number",
			"subfields" : null
		},
		"082" : {
			"name"      : "Dewey Decimal Classification Number",
			"subfields" : null
		},
		"083" : {
			"name"      : "Additional Dewey Decimal Classification Number",
			"subfields" : null
		},
		"084" : {
			"name"      : "分類",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "分類記号",
					"required"  : true,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "分類法",
					"required"  : false,
					"repeat"    : false,
					"default"   : ["kktb", "njb/09"]
				}
			]
		},
		"085" : {
			"name"      : "Synthesized Classification Number Components",
			"subfields" : null
		},
		"086" : {
			"name"      : "Government Document Classification Number",
			"subfields" : null
		},
		"088" : {
			"name"      : "Report Number",
			"subfields" : null
		},
		"090" : {
			"name"      : "請求記号",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : true,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "請求記号",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"098" : {
			"name"      : "地図各種番号",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"type"      : "map",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "UTM区画番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "全国地方公共団体コード",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "d",
					"name"      : "各国国内海図番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "e",
					"name"      : "国際海図番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"100" : {
			"name"      : "Main Entry - Personal Name",
			"subfields" : null
		},
		"110" : {
			"name"      : "Main Entry - Corporate Name",
			"subfields" : null
		},
		"111" : {
			"name"      : "Main Entry - Meeting Name",
			"subfields" : null
		},
		"130" : {
			"name"      : "Main Entry - Uniform Title",
			"subfields" : null
		},
		"210" : {
			"name"      : "キータイトル略語形",
			"ind1"      : "1",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "キータイトル略語形",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "キータイトル略語形の識別情報",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"222" : {
			"name"      : "キータイトル",
			"ind1"      : " ",
			"ind2"      : "0",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "キータイトル",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "キータイトル識別情報",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"240" : {
			"name"      : "Uniform Title",
			"subfields" : null
		},
		"242" : {
			"name"      : "Translation of Title by Cataloging Agency",
			"subfields" : null
		},
		"243" : {
			"name"      : "Collective Uniform Title",
			"subfields" : null
		},
		"245" : {
			"name"      : "タイトル・責任表示事項",
			"ind1"      : "0",
			"ind2"      : "0",
			"required"  : true,
			"repeat"    : false,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "本タイトル",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "タイトル関連情報",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "責任表示",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "h",
					"name"      : "資料種別",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "n",
					"name"      : "巻次・部編番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "p",
					"name"      : "部編名",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"246" : {
			"name"      : "別タイトル",
			"ind1"      : ["2", "0", "1", "3"],
			"ind2"      : ["1", " ", "0", "3", "4", "7", "8"],
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "別タイトル",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "別タイトル関連情報",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "g",
					"name"      : "付記事項",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "i",
					"name"      : "説明句",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "n",
					"name"      : "別タイトル部編番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "p",
					"name"      : "別タイトル部編名",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"247" : {
			"name"      : "Former Title",
			"subfields" : null
		},
		"250" : {
			"name"      : "版に関する事項",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "版表示",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "版の責任表示",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"254" : {
			"name"      : "Musical Presentation Statement",
			"subfields" : null
		},
		"255" : {
			"name"      : "数値データ事項（地図資料）",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "map",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "縮尺",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "投影法表示",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "経緯度",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"256" : {
			"name"      : "電子的内容（電子資料）",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"type"      : "electronic",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "電子的内容",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"257" : {
			"name"      : "Country of Producing Entity",
			"subfields" : null
		},
		"258" : {
			"name"      : "Philatelic Issue Data",
			"subfields" : null
		},
		"260" : {
			"name"      : "出版・頒布に関する事項",
			"ind1"      : [" ", "2", "3"],
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "出版地・頒布地等",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "出版者・頒布者等",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "出版年月・頒布年月等",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"263" : {
			"name"      : "Projected Publication Date",
			"subfields" : null
		},
		"264" : {
			"name"      : "Production, Publication, Distribution, Manufacture, and Copyright Notice",
			"subfields" : null
		},
		"270" : {
			"name"      : "Address",
			"subfields" : null
		},
		"300" : {
			"name"      : "形態に関する事項",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : true,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "特定資料種別と数量",
					"required"  : true,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "形態的細目",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "大きさ",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "e",
					"name"      : "付属資料",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"306" : {
			"name"      : "Playing Time",
			"subfields" : null
		},
		"307" : {
			"name"      : "Hours, etc.",
			"subfields" : null
		},
		"310" : {
			"name"      : "刊行頻度",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "刊行頻度",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"321" : {
			"name"      : "Former Publication Frequency",
			"subfields" : null
		},
		"336" : {
			"name"      : "Content Type",
			"subfields" : null
		},
		"337" : {
			"name"      : "Media Type",
			"subfields" : null
		},
		"338" : {
			"name"      : "Carrier Type",
			"subfields" : null
		},
		"340" : {
			"name"      : "Physical Medium",
			"subfields" : null
		},
		"342" : {
			"name"      : "Geospatial Reference Data",
			"subfields" : null
		},
		"343" : {
			"name"      : "Planar Coordinate Data",
			"subfields" : null
		},
		"344" : {
			"name"      : "Sound Characteristics",
			"subfields" : null
		},
		"345" : {
			"name"      : "Projection Characteristics of Moving Image",
			"subfields" : null
		},
		"346" : {
			"name"      : "Video Characteristics",
			"subfields" : null
		},
		"347" : {
			"name"      : "Digital File Characteristics",
			"subfields" : null
		},
		"351" : {
			"name"      : "Organization and Arrangement of Materials",
			"subfields" : null
		},
		"352" : {
			"name"      : "Digital Graphic Representation",
			"subfields" : null
		},
		"355" : {
			"name"      : "Security Classification Control",
			"subfields" : null
		},
		"357" : {
			"name"      : "Originator Dissemination Control",
			"subfields" : null
		},
		"362" : {
			"name"      : "巻次・年月次／休・廃刊注記",
			"ind1"      : ["0", "1"],
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : false,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "巻次・年月次／休・廃刊注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"363" : {
			"name"      : "Normalized Date and Sequential Designation",
			"subfields" : null
		},
		"365" : {
			"name"      : "Trade Price",
			"subfields" : null
		},
		"366" : {
			"name"      : "Trade Availability Information",
			"subfields" : null
		},
		"377" : {
			"name"      : "Associated Language",
			"subfields" : null
		},
		"380" : {
			"name"      : "Form of Work",
			"subfields" : null
		},
		"381" : {
			"name"      : "Other Distinguishing Characteristics of Work or Expression",
			"subfields" : null
		},
		"382" : {
			"name"      : "Medium of Performance",
			"subfields" : null
		},
		"383" : {
			"name"      : "Numeric Designation of Musical Work",
			"subfields" : null
		},
		"384" : {
			"name"      : "Key",
			"subfields" : null
		},
		"490" : {
			"name"      : "シリーズに関する事項",
			"ind1"      : "0", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "シリーズタイトル等",
					"required"  : true,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "シリーズISSN",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "v",
					"name"      : "シリーズ番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"500" : {
			"name"      : "一般注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "一般注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"501" : {
			"name"      : "With Note",
			"subfields" : null
		},
		"502" : {
			"name"      : "Dissertation Note",
			"subfields" : null
		},
		"504" : {
			"name"      : "書誌注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "書誌注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"505" : {
			"name"      : "内容注記",
			"ind1"      : "0", 
			"ind2"      : ["0", " "],
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "内容注記",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "g",
					"name"      : "その他の情報",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "r",
					"name"      : "責任表示",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "t",
					"name"      : "タイトル",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"506" : {
			"name"      : "Restrictions on Access Note",
			"subfields" : null
		},
		"507" : {
			"name"      : "Scale Note for Graphic Material",
			"subfields" : null
		},
		"508" : {
			"name"      : "Creation/Production Credits Note",
			"subfields" : null
		},
		"510" : {
			"name"      : "Citation/References Note",
			"subfields" : null
		},
		"511" : {
			"name"      : "出演者注記",
			"ind1"      : "0", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : ["audio", "visual"],
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "出演者注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"513" : {
			"name"      : "Type of Report and Period Covered Note",
			"subfields" : null
		},
		"514" : {
			"name"      : "Data Quality Note",
			"subfields" : null
		},
		"515" : {
			"name"      : "Numbering Peculiarities Note",
			"subfields" : null
		},
		"516" : {
			"name"      : "電子的内容注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "electronic",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "電子的内容注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"518" : {
			"name"      : "日時・場所に関する注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : ["audio", "visual"],
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "日時・場所に関する注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"520" : {
			"name"      : "Summary, etc.",
			"subfields" : null
		},
		"521" : {
			"name"      : "Target Audience Note",
			"subfields" : null
		},
		"522" : {
			"name"      : "Geographic Coverage Note",
			"subfields" : null
		},
		"524" : {
			"name"      : "Preferred Citation of Described Materials Note",
			"subfields" : null
		},
		"525" : {
			"name"      : "Supplement Note",
			"subfields" : null
		},
		"526" : {
			"name"      : "Study Program Information Note",
			"subfields" : null
		},
		"530" : {
			"name"      : "別の媒体に関する注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "別の媒体に関する注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"533" : {
			"name"      : "Reproduction Note",
			"subfields" : null
		},
		"534" : {
			"name"      : "原資料に関する注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "c",
					"name"      : "原資料の出版事項",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "l",
					"name"      : "原資料の所蔵情報",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "n",
					"name"      : "原資料に関する注記",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "p",
					"name"      : "説明句",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "t",
					"name"      : "原資料のタイトル",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"535" : {
			"name"      : "Location of Originals/Duplicates Note",
			"subfields" : null
		},
		"536" : {
			"name"      : "Funding Information Note",
			"subfields" : null
		},
		"538" : {
			"name"      : "システム要件に関する注記（電子資料）",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "electronic",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "システム要件に関する注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"540" : {
			"name"      : "Terms Governing Use and Reproduction Note",
			"subfields" : null
		},
		"541" : {
			"name"      : "Immediate Source of Acquisition Note",
			"subfields" : null
		},
		"542" : {
			"name"      : "Information Relating to Copyright Status",
			"subfields" : null
		},
		"544" : {
			"name"      : "Location of Other Archival Materials Note",
			"subfields" : null
		},
		"545" : {
			"name"      : "Biographical or Historical Data",
			"subfields" : null
		},
		"546" : {
			"name"      : "言語注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "言語注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"547" : {
			"name"      : "Former Title Complexity Note",
			"subfields" : null
		},
		"550" : {
			"name"      : "出版・頒布に関する注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "出版・頒布に関する注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"552" : {
			"name"      : "Entity and Attribute Information Note",
			"subfields" : null
		},
		"555" : {
			"name"      : "総目次・総索引注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "総目次・総索引注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"556" : {
			"name"      : "Information About Documentation Note",
			"subfields" : null
		},
		"561" : {
			"name"      : "Ownership and Custodial History",
			"subfields" : null
		},
		"562" : {
			"name"      : "Copy and Version Identification Note",
			"subfields" : null
		},
		"563" : {
			"name"      : "装丁に関する注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "装丁に関する注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"565" : {
			"name"      : "Case File Characteristics Note",
			"subfields" : null
		},
		"567" : {
			"name"      : "Methodology Note",
			"subfields" : null
		},
		"580" : {
			"name"      : "記入リンクに関する注記",
			"ind1"      : " ", 
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "記入リンクに関する注記",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"581" : {
			"name"      : "Publications About Described Materials Note",
			"subfields" : null
		},
		"583" : {
			"name"      : "Action Note",
			"subfields" : null
		},
		"584" : {
			"name"      : "Accumulation and Frequency of Use Note",
			"subfields" : null
		},
		"585" : {
			"name"      : "Exhibitions Note",
			"subfields" : null
		},
		"586" : {
			"name"      : "Awards Note",
			"subfields" : null
		},
		"588" : {
			"name"      : "Source of Description Note",
			"subfields" : null
		},
		"600" : {
			"name"      : "個人名件名標目",
			"ind1"      : ["1", "3"],
			"ind2"      : "7",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "個人名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "世系",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "その他の付記事項",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "d",
					"name"      : "生没年",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "情報源",
					"required"  : true,
					"repeat"    : false,
					"default"   : "ndlsh"
				}
			]
		},
		"610" : {
			"name"      : "団体名件名標目",
			"ind1"      : "2",
			"ind2"      : "7",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "団体名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "情報源",
					"required"  : true,
					"repeat"    : false,
					"default"   : "ndlsh"
				}
			]
		},
		"611" : {
			"name"      : "Subject Added Entry - Meeting Name",
			"subfields" : null
		},
		"630" : {
			"name"      : "統一タイトル件名標目",
			"ind1"      : "0",
			"ind2"      : "7",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "統一タイトル件名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "v",
					"name"      : "形式細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "主題細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "y",
					"name"      : "時代細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "地名細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "情報源",
					"required"  : true,
					"repeat"    : false,
					"default"   : "ndlsh"
				}
			]
		},
		"648" : {
			"name"      : "Subject Added Entry - Chronological Term",
			"subfields" : null
		},
		"650" : {
			"name"      : "普通件名標目",
			"ind1"      : " ",
			"ind2"      : "7",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "普通件名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "v",
					"name"      : "形式細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "主題細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "y",
					"name"      : "時代細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "地名細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "情報源",
					"required"  : true,
					"repeat"    : false,
					"default"   : "ndlsh"
				}
			]
		},
		"651" : {
			"name"      : "地名件名標目",
			"ind1"      : " ",
			"ind2"      : "7",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "地名件名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "v",
					"name"      : "形式細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "主題細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "y",
					"name"      : "時代細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "地名細目",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "情報源",
					"required"  : true,
					"repeat"    : false,
					"default"   : "ndlsh"
				}
			]
		},
		"653" : {
			"name"      : "Index Term - Uncontrolled",
			"subfields" : null
		},
		"654" : {
			"name"      : "Subject Added Entry - Faceted Topical Terms",
			"subfields" : null
		},
		"655" : {
			"name"      : "Index Term - Genre/Form",
			"subfields" : null
		},
		"656" : {
			"name"      : "Index Term - Occupation",
			"subfields" : null
		},
		"657" : {
			"name"      : "Index Term - Function",
			"subfields" : null
		},
		"658" : {
			"name"      : "Index Term - Curriculum Objective",
			"subfields" : null
		},
		"662" : {
			"name"      : "Subject Added Entry - Hierarchical Place Name",
			"subfields" : null
		},
		"700" : {
			"name"      : "個人著者標目",
			"ind1"      : "1", 
			"ind2"      : [" ", "2"],
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "個人名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "世系",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "その他の付記事項",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "d",
					"name"      : "生没年",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"710" : {
			"name"      : "団体著者標目",
			"ind1"      : "2", 
			"ind2"      : [" ", "2"],
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "団体名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"711" : {
			"name"      : "Added Entry - Meeting Name",
			"subfields" : null
		},
		"720" : {
			"name"      : "非統制標目",
			"ind1"      : "2",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "非統制標目",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"730" : {
			"name"      : "Added Entry - Uniform Title",
			"subfields" : null
		},
		"740" : {
			"name"      : "その他のタイトル標目",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "その他のタイトル標目",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"751" : {
			"name"      : "Added Entry - Geographic Name",
			"subfields" : null
		},
		"752" : {
			"name"      : "Added Entry - Hierarchical Place Name",
			"subfields" : null
		},
		"753" : {
			"name"      : "System Details Access to Computer Files",
			"subfields" : null
		},
		"754" : {
			"name"      : "Added Entry - Taxonomic Identification",
			"subfields" : null
		},
		"760" : {
			"name"      : "上位シリーズ記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "x",
					"name"      : "上位シリーズのISSN",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"762" : {
			"name"      : "下位シリーズ記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "x",
					"name"      : "下位シリーズのISSN",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"765" : {
			"name"      : "原言語版記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "x",
					"name"      : "原言語版のISSN",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"767" : {
			"name"      : "他言語版記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "x",
					"name"      : "他言語版のISSN",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"770" : {
			"name"      : "挿入誌・付録誌記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "x",
					"name"      : "挿入誌・付録誌のISSN",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"772" : {
			"name"      : "本体誌記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "x",
					"name"      : "本体誌のISSN",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"773" : {
			"name"      : "Host Item Entry",
			"subfields" : null
		},
		"774" : {
			"name"      : "Constituent Unit Entry",
			"subfields" : null
		},
		"775" : {
			"name"      : "異版記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "t",
					"name"      : "リンク先レコードのタイトル",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "w",
					"name"      : "リンク先レコードの管理番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "リンク先レコードのISSN",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"776" : {
			"name"      : "他媒体版記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "x",
					"name"      : "他媒体版のISSN",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"777" : {
			"name"      : "Issued With Entry",
			"subfields" : null
		},
		"780" : {
			"name"      : "先行記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : ["0", "1", "4", "5", "6", "7"],
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "t",
					"name"      : "リンク先レコードのタイトル",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "g",
					"name"      : "改題発生巻次・年月次",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "w",
					"name"      : "リンク先レコードの管理番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "リンク先レコードのISSN",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"785" : {
			"name"      : "後継記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : ["0", "1", "4", "5", "6", "7"],
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "t",
					"name"      : "リンク先レコードのタイトル",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "g",
					"name"      : "改題発生巻次・年月次",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "w",
					"name"      : "リンク先レコードの管理番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "リンク先レコードのISSN",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"786" : {
			"name"      : "Data Source Entry",
			"subfields" : null
		},
		"787" : {
			"name"      : "関連記入（逐次刊行物）",
			"ind1"      : "0",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "i",
					"name"      : "関連の種別に関する情報",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "t",
					"name"      : "リンク先レコードのタイトル",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "w",
					"name"      : "リンク先レコードの管理番号",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "リンク先レコードのISSN",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"800" : {
			"name"      : "個人著者標目（シリーズ）",
			"ind1"      : "1",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "個人名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "世系",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "c",
					"name"      : "その他の付記事項",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "d",
					"name"      : "生没年",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"810" : {
			"name"      : "団体著者標目（シリーズ）",
			"ind1"      : "2",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : false,
					"repeat"    : false,
					"default"   : "link1"
				},
				{
					"code"      : "a",
					"name"      : "団体名",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "典拠レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"811" : {
			"name"      : "Series Added Entry - Meeting Name",
			"subfields" : null
		},
		"830" : {
			"name"      : "Series Added Entry - Uniform Title",
			"subfields" : null
		},
		"841" : {
			"name"      : "Holdings Coded Data Values",
			"subfields" : null
		},
		"842" : {
			"name"      : "Textual Physical Form Designator",
			"subfields" : null
		},
		"843" : {
			"name"      : "Reproduction Note",
			"subfields" : null
		},
		"844" : {
			"name"      : "Name of Unit",
			"subfields" : null
		},
		"845" : {
			"name"      : "Terms Governing Use and Reproduction",
			"subfields" : null
		},
		"850" : {
			"name"      : "Holding Institution",
			"subfields" : null
		},
		"853" : {
			"name"      : "Captions and Pattern - Basic Bibliographic Unit",
			"subfields" : null
		},
		"854" : {
			"name"      : "Captions and Pattern - Supplementary Material ",
			"subfields" : null
		},
		"855" : {
			"name"      : "Captions and Pattern - Indexes",
			"subfields" : null
		},
		"856" : {
			"name"      : "電子資料アクセス情報",
			"ind1"      : ["4", "0", "1", "7"],
			"ind2"      : ["0", "2"],
			"required"  : false,
			"repeat"    : true,
			"type"      : "electronic",
			"subfields" : [
				{
					"code"      : "u",
					"name"      : "URI",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				},
				{
					"code"      : "2",
					"name"      : "アクセス方法",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"863" : {
			"name"      : "Enumeration and Chronology - Basic Bibliographic Unit",
			"subfields" : null
		},
		"864" : {
			"name"      : "Enumeration and Chronology - Supplementary Material",
			"subfields" : null
		},
		"865" : {
			"name"      : "Enumeration and Chronology - Indexes",
			"subfields" : null
		},
		"866" : {
			"name"      : "所蔵に関する事項（逐次刊行資料）",
			"ind1"      : " ",
			"ind2"      : "0",
			"required"  : false,
			"repeat"    : true,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "所蔵順序表示",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "欠号情報／所蔵注記",
					"required"  : false,
					"repeat"    : true,
					"default"   : null
				}
			]
		},
		"867" : {
			"name"      : "Textual Holdings - Supplementary Material",
			"subfields" : null
		},
		"868" : {
			"name"      : "Textual Holdings - Indexes",
			"subfields" : null
		},
		"876" : {
			"name"      : "Item Information - Basic Bibliographic Unit",
			"subfields" : null
		},
		"877" : {
			"name"      : "Item Information - Supplementary Material",
			"subfields" : null
		},
		"878" : {
			"name"      : "Item Information - Indexes",
			"subfields" : null
		},
		"880" : {
			"name"      : "対応する読み",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : false,
			"repeat"    : true,
			"subfields" : [
				{
					"code"      : "6",
					"name"      : "読みの対応関係",
					"required"  : true,
					"repeat"    : false,
					"default"   : "link2"
				},
				{
					"code"      : "a",
					"name"      : "タイトル・名称・出版地等",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "b",
					"name"      : "関連情報・世系・出版者等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "d",
					"name"      : "生没年等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "n",
					"name"      : "巻次・部編番号等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "p",
					"name"      : "部編名等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "v",
					"name"      : "シリーズ番号・形式細目等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "x",
					"name"      : "主題細目等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "y",
					"name"      : "時代細目等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "z",
					"name"      : "地名細目等",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "0",
					"name"      : "リンク先レコード管理番号",
					"required"  : false,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"882" : {
			"name"      : "Replacement Record Information",
			"subfields" : null
		},
		"886" : {
			"name"      : "Foreign MARC Information Field",
			"subfields" : null
		},
		"887" : {
			"name"      : "Non-MARC Information Field",
			"subfields" : null
		},
		"852" : {
			"name"      : "配架記号",
			"ind1"      : " ",
			"ind2"      : " ",
			"required"  : true,
			"repeat"    : false,
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "配架記号",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		},
		"963" : {
			"name"      : "所蔵巻年次（逐次刊行資料）",
			"ind1"      : " ",
			"ind2"      : "0",
			"required"  : false,
			"repeat"    : false,
			"type"      : "serial",
			"subfields" : [
				{
					"code"      : "a",
					"name"      : "所蔵巻号",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				},
				{
					"code"      : "i",
					"name"      : "所蔵年次",
					"required"  : true,
					"repeat"    : false,
					"default"   : null
				}
			]
		}
	}';

	private $all_fields = null;
	
	private $use_fields = null;
	
	private static $instance = null;
	
	private function __construct() {
		$this->all_fields = json_decode(self::$fields_json, true);
		
		$temp = array();
		foreach ($this->all_fields as $tag => $obj) {
			if (!empty($obj['control']) && !empty($obj['required'])) {
				array_push($temp, $tag);
			} else if (empty($obj['control']) && !(empty($obj['subfields']))) {
				array_push($temp, $tag);
			}
		}
		$this->use_fields = $temp;
	}
				
	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new MARCFields();
		}
				
		return self::$instance;
	}
			
	public function all_fields() {
		return $this->all_fields;
	}

	public function use_fields() {
		return $this->use_fields;
	}
}

?>
