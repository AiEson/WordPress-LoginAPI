<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header('Access-Control-Allow-Headers:x-requested-with,content-type');
//引入WP加载文件，引入之后就可以使用WP的所有函数 
require( './wp-load.php' );

//img标签解析URL
function json_get_avatar_url( $avatar_html ) {

    // Strip the avatar url from the get_avatar img tag.
    preg_match('/src=["|\'](.+)[\&|"|\']/U', $avatar_html, $matches);

    if ( isset( $matches[1] ) && ! empty( $matches[1] ) ) {
        return esc_url_raw( $matches[1] );
    }

    return '';
}
 
//定义返回数组，默认先为空
$data=[];
 
//1、接收post参数。
$user_name = $_POST["username"];
$user_pwd = $_POST["password"];
$login_type = $_POST["type"];
if($user_name==''||$user_pwd==''){
	$data['code'] = 404;
	$data['msg'] = '请认真填写表单！';
}else{
	// 2、收集登录数据
	$login_data['user_login'] = $user_name;
	$login_data['user_password'] = $user_pwd;
//使用wp函数校验用户名、密码
	$user_verify =wp_signon( $login_data, false ); 
	if ( is_wp_error($user_verify) ) {   
		$data["code"] = 404;
		$data["msg"] = "用户名或者密码错误！";
	}else{  
		$user=get_user_by($login_type,$user_name);
		$user_id=$user->ID;                          
		$userinfo= get_userdata($user_id); 
		$data['status']=2;  
		$data['msg']='登录成功！欢迎回来！';
		$data['user_info']['user_id']=$user_id;
		$data['user_info']['user_name']=$userinfo->user_login;
		$data['user_info']['user_avatar']=json_get_avatar_url(get_avatar($user_id));
	}
}
// 输出json数据格式
print_r(json_encode($data));
?>