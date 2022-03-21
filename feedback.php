<?
namespace Wss;

class Feedback
{
    static public $iblock_id_order = 37;// ИБ запросов с форм
    static public function isAuth(){
        return \Wss\Auth::is($_REQUEST['login'], $_REQUEST['hash'], false)['isAuth'];
    }
    static public function send($request)
    {
        if(\CModule::IncludeModule("iblock") && self::isAuth()) {
            global $USER;
            $json = array();
            /*** 684 - USER_FROM */

            $rsUser = \CUser::GetByID($USER->GetID());
            $arUser = $rsUser->Fetch();
            $name = '';
            if($arUser['LAST_NAME']) $name = $arUser['LAST_NAME'];
            if($arUser['NAME']) $name = $name ? $name.' '.$arUser['NAME'] : $arUser['NAME'];
            if($arUser['SECOND_NAME']) $name = $name ? $name.' '.$arUser['SECOND_NAME'] : $arUser['SECOND_NAME'];

            $el = new \CIBlockElement;
            $PROP = array();
            $PROP[684] = $arUser['ID'];  // привязка к пользователю
            $arLoadProductArray = Array(
                "MODIFIED_BY"    => $arUser['ID'],
                "IBLOCK_ID"      => self::$iblock_id_order,
                "PROPERTY_VALUES"=> $PROP,
                "PREVIEW_TEXT"=> $request['comment'],
                "NAME" => "Обратная связь от {$name}"
            );

            if($PRODUCT_ID = $el->Add($arLoadProductArray)){
                $json['status'] = 'success';
                $json['message'] = $PRODUCT_ID;
            }else{
                $json['status'] = 'error';
                $json['message'] = $el->LAST_ERROR;
            }
            unset($rsUser,$arUser,$name,$el,$PROP,$arLoadProductArray);
        }else{
            $json['status'] = 'error';
            $json['message'] = 'Вы не авторизованы';
        }
        return json_encode($json);
    }
}
