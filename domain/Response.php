<?php
define('SUCCESS', 'ok');
/**
 * 返回对象
 *
 * Class Response
 */
class Response
{
    //代码：0成功，1失败
    private $code = 0;

    //消息："ok"
    private $msg = SUCCESS;

    //数据：泛型
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __toString(): string
    {
        return json_encode(array("code" => $this->code, "msg" => $this->msg, "data" => $this->data), JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * @param mixed $msg
     */
    public function setMsg($msg)
    {
        if ($msg != SUCCESS) $this->code = 1;
        $this->msg = $msg;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

}