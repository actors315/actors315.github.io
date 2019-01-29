---  
layout: post  
title: '通过唯一ID实现简单的日志跟踪实现'  
date: 2018-10-14T11:28:34+08:00  
excerpt: '在实际项目中，通知我们需要记录一些日志，方便问题核查。但是日志多了就很容易混乱，请求，响应，执行中的日志无法对应，这时就需要为请求进行标记唯一ID来进行跟踪。
/**
 * 记录请求日志
 *
 * '  
---  

在实际项目中，通知我们需要记录一些日志，方便问题核查。但是日志多了就很容易混乱，请求，响应，执行中的日志无法对应，这时就需要为请求进行标记唯一ID来进行跟踪。

```
/**
 * 记录请求日志
 *
 * Class ApiLog
 * @package App\Library\Components\Elog
 */
class ApiLog
{
    static $logPath;

    private static $singleton;

    
    /**
     * 单例
     * @return ApiLog
     */
    public static function singleton()
    {

        if (false == self::$singleton instanceof ApiLog) {
            self::$singleton = new static();
        }

        return self::$singleton;
    }

    protected function __construct($logPath = '')
    {
        if (empty($logPath)) {
            self::$logPath = ROOT_PATH . 'logs/request/';
        } else {
            self::$logPath = ROOT_PATH . $logPath;
        }

        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0777, true);
        }
    }

    public function record($action, $request = [], $type = 'requestLog')
    {
        $headers = [];
        if (!function_exists('getallheaders')) {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value;
                }
            }
        } else {
            $headers = getallheaders();
        }

        //==============  加密用户登录密码
        if (isset($request['password'])) {
            $request['password'] = md5(md5($request['password']));
        }

        //==============  加密邮箱
        if (isset($request['email'])) {
            $request['email'] = encrypt_email($request['email']);
        }
        // ...... 日志中对关键信息进行加密
        
        // 请求日志记录详细一点
        if ('requestLog' == $type) {
            $data = [
                'action' => $action,
                'platform' => PHONE_SYSTEM,
                'ip' => real_ip(),
                'request' => $request,
                'REQUEST_URI' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '',
                'headers' => $headers,
            ];
        } else {
            $data = [
                'action' => $action,
                'response' => $request
            ];
        }

        $this->write($data, $type);
    }

    protected function write($logData, $type)
    {
    
        $minutes = date('i');

        $file = date('Y-m-d-H') . '-v' . (intval($minutes / 10)) . '.log';

        $logData = ['request_id' => static::getRequestId(), 'add_time' => time(), 'type' => $type, 'content' => $logData];

        file_put_contents(self::$logPath . $file, json_encode($logData) . PHP_EOL, FILE_APPEND);
    }

    protected static function getRequestId()
    {
        static $requestId = '';

        if (!empty($requestId)) {
            return $requestId;
        }

        if (function_exists('session_create_id')) {
            $hash = session_create_id();
        } else {
            $uid = uniqid('', true);
            $data = '';
            $data .= isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : '';
            $data .= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
            $data .= isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : '';
            $data .= isset($_SERVER['LOCAL_PORT']) ? $_SERVER['LOCAL_PORT'] : '';
            $data .= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
            $data .= isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '';
            $hash = hash('ripemd128', $uid . md5($data));
        }

        $hash = strtoupper($hash);

        return $requestId = substr($hash, 0, 8) . '-' . substr($hash, 8, 4) . '-' . substr($hash, 12, 4) . '-' . substr($hash, 16, 4) . '-' . substr($hash, 20, 12);
    }
}
```

使用单例，保证一次请求的ID一致

```

ApiLog::singleton()->record($action,$request);

ApiLog::singleton()->record($action,$actionData,'createOrder');

ApiLog::singleton()->record($action,$errorMessage,'errorHandler');
       
ApiLog::singleton()->record($action,$response,'ResponseLog');

```