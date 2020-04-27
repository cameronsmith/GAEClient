<?php namespace UKCASmith\GAEClient\Data\Requests;

class Version
{
    /**
     * @var string
     */
    protected $str_id;
    /**
     * @var string
     */
    protected $str_instance_class = 'F1';
    /**
     * @var string
     */
    protected $str_runtime = 'php55';
    /**
     * @var bool
     */
    protected $bol_thread_safe = true;
    /**
     * @var string
     */
    protected $str_env = 'standard';

    /**
     * @var string
     */
    protected $str_deployment_zip;

    /**
     * @var string
     */
    protected $str_url_regex = '.*';

    /**
     * @return string
     */
    public function getStrUrlRegex()
    {
        return $this->str_url_regex;
    }

    /**
     * @param string $str_url_regex
     * @return Version
     */
    public function setStrUrlRegex($str_url_regex)
    {
        $this->str_url_regex = $str_url_regex;
        return $this;
    }

    /**
     * @var string
     */
    protected $str_script_path = 'index.php';

    /**
     * @return string
     */
    public function getId()
    {
        return $this->str_id;
    }

    /**
     * @param string $str_id
     * @return Version
     */
    public function setId($str_id)
    {
        $this->str_id = $str_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstanceClass()
    {
        return $this->str_instance_class;
    }

    /**
     * @param string $str_instance_class
     * @return Version
     */
    public function setInstanceClass($str_instance_class)
    {
        $this->str_instance_class = $str_instance_class;
        return $this;
    }

    /**
     * @return string
     */
    public function getRuntime()
    {
        return $this->str_runtime;
    }

    /**
     * @param string $str_runtime
     * @return Version
     */
    public function setRuntime($str_runtime)
    {
        $this->str_runtime = $str_runtime;
        return $this;
    }

    /**
     * @return bool
     */
    public function isThreadSafe()
    {
        return $this->bol_thread_safe;
    }

    /**
     * @param bool $bol_thread_safe
     * @return Version
     */
    public function setThreadSafe($bol_thread_safe)
    {
        $this->bol_thread_safe = $bol_thread_safe;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->str_env;
    }

    /**
     * @param string $str_env
     * @return Version
     */
    public function setEnv($str_env)
    {
        $this->str_env = $str_env;
        return $this;
    }

    /**
     * @return string
     */
    public function getDeploymentZip()
    {
        return $this->str_deployment_zip;
    }

    /**
     * @param string $str_deployment_zip
     * @return Version
     */
    public function setDeploymentZip($str_deployment_zip)
    {
        $this->str_deployment_zip = $str_deployment_zip;
        return $this;
    }

    /**
     * @return string
     */
    public function getStrScriptPath()
    {
        return $this->str_script_path;
    }

    /**
     * @param string $str_script_path
     * @return Version
     */
    public function setStrScriptPath($str_script_path)
    {
        $this->str_script_path = $str_script_path;
        return $this;
    }
}