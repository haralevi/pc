<?php
/**
 * Created by Andre Haralevi
 * Date: 06.11.13
 * Time: 22:14
 */

namespace photocommunity\mobile;

class Tpl
{
    public $content, $path, $ext, $file_name, $info_path, $vars;

    function __construct()
    {
        $this->path = Config::$documentRoot . Config::$templatePath;
        $this->ext = Config::$templateExt;
    }

    public function open($filename)
    {
        if ($filename == '')
            return '';
        $path = $this->path;
        $ext = $this->ext;
        $fd = fopen($path . $filename . '.' . $ext, 'r');
        if ($fd == false)
            return false;

        $content = fread($fd, filesize($path . $filename . '.' . $ext));
        fclose($fd);
        $this->file_name = $filename;
        $this->content = $content;
        return true;
    }

    public function block($what, $cnt = '')
    {
        if ($cnt == '') $content = $this->content;
        else $content = $cnt;
        preg_match('/<\!--\[' . $what . '\]-->(.*?)<\!--\[' . $what . '\]-->/ms', $content, $matches);
        $block = $matches[1];
        return $block;
    }

    public function replaceFld($what, $to, $cnt = '')
    {
        $to = str_replace('&quot;', '"', $to);
        $to = htmlentities(stripslashes($to), ENT_QUOTES, 'UTF-8');
        if ($cnt == '')
            $content = $this->content; else $content = $cnt;
        $content = str_replace('#' . $what . '#', $to, $content);
        if ($cnt == '')
            $this->content = $content;
        return $content;
    }

    public function replace($what, $to, $cnt = '')
    {
        if ($cnt == '') $content = $this->content; else $content = $cnt;
        $content = str_replace('{' . $what . '}', $to, $content);
        if ($cnt == '')
            $this->content = $content;
        return $content;
    }

    public function clear($what, $cnt = '')
    {
        if ($cnt == '') $content = $this->content; else $content = $cnt;
        $content = preg_replace('/<\!--\[' . $what . '\]-->.*?<\!--\[' . $what . '\]-->/ms', '', $content);
        $content = str_replace('#' . $what . '#', '', $content);
        if ($cnt == '')
            $this->content = $content;
        return $content;
    }

    public function get()
    {
        return $this->content;
    }

    public function set($what, $cnt = '')
    {
        if ($cnt == '') $content = $this->content;
        else $content = $cnt;

        $block = $this->block($what, $content);
        $content = $this->replace($what, $block, $content);

        if ($cnt == '') $this->content = $content;
        return $content;
    }

    private function callbackReplace($matches)
    {
        $this->content = str_replace($matches[0], $this->vars[$matches[1]], $this->content);
    }

    public function parse($tpl_var)
    {
        $this->vars = $tpl_var;
        preg_replace_callback('/\{(\w+)\}/',
            function ($matches) {
                $this->callbackReplace($matches);
            },
            $this->content);
    }
}