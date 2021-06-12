<?php
define('SUCCESS', 'success');

/**
 * 返回对象
 *
 * Class Response
 */
class Response
{
    //代码：1成功，0失败
    private $code = 1;

    //消息："success","fail"
    private $msg = SUCCESS;

    //数据：泛型
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function __toString()
    {
        return json_encode(array("code" => $this->code, "msg" => $this->msg, "data" => $this->data));
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
        if ($msg != SUCCESS) $this->code = 0;
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