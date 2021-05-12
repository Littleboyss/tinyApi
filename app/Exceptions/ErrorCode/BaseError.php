<?php
namespace App\Exceptions\ErrorCode;
use App\Util\TemplateUtil;

class BaseError{
	// key[0]:err_code  key[1]:cn_msg key[2]:en_msg
	public static function throw_exception($key,$values=array()){
        $msg_key = self::getLang($key);
		$msg = TemplateUtil::parseTemplate($key[$msg_key],$values);
        throw new DaoException($msg, $key[0]);
	}
	
	public static function make_ret($key,$values=array()){
        $msg_key = self::getLang($key);
		$msg = TemplateUtil::parseTemplate($key[$msg_key],$values);
		return array('result'=>$key[0],'res_info'=>$msg);
	}

	public static function return_success(){
		return array('result'=>0, 'res_info'=>'ok');
	}

    public static function return_success_with_data(array $result){
        $ret = array('result'=>0, 'res_info'=>'ok');
        $ret = array_merge($ret, $result);
        return $ret;
    }

    public static function return_error_with_data($key, $data = array(), $values = array()){
        $msg_key = self::getLang($key);
        $msg = TemplateUtil::parseTemplate($key[$msg_key],$values);
        $ret = array('result'=>$key[0],'res_info'=>$msg);
        $ret = array_merge($ret, $data);
        return $ret;
    }

	public static function return_error($errcode,$errmsg){
		return array('result'=>$errcode,'res_info'=>$errmsg);
	}

    public static function return_error_by_custom($errcode,$errmsg,$data= array()){
        $ret = array('result'=>$errcode,'res_info'=>$errmsg);
        $ret = array_merge($ret, $data);
        return $ret;
    }

	public static function getCode(Array $arr){
		return $arr[0];
	}

	public static function getMessage(Array $arr){
        $msg_key = self::getLang($arr);
		return $arr[$msg_key];
	}

    /**
     * 获取语言
     * 
     * @param $key_arr  错误码数组
     * 
     * @return int  对应msg的中英文
     */
    private static function getLang($key_arr) {
        $lang_arr = array('CN' => 1, 'EN' => 2);
        $lang_key = isset($_REQUEST['lang']) && in_array($_REQUEST['lang'], $lang_arr) ? $_REQUEST['lang'] : 1;
        if($lang_key == 2 && !array_key_exists($lang_key, $key_arr)){
            $lang_key = 1;
        }
        return $lang_key;
    }


}