<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus\Web;

use \Psr\Http\Message\ServerRequestInterface;

/**
 * Simple view implementation using php as a templating language.
 */
class View
{
    /**
     * Content that is prepended to the renderized view. Useful for debugging.
     * @var string;
     */
    public static $pre_template_content;

    /**
     * Content that is apended to the renderized view. Useful for debugging.
     * @var string;
     */
    public static $post_template_content;

    /**
     * @var \Psr\Http\Message\ServerRequestInterface
     */
    public $request;

    /**
     * List of javascript files that may get included on the template file.
     * @var string[]
     */
    public $scripts;

    /**
     * List of javascript code that may get included on the template file.
     * @var string[]
     */
    public $scripts_code;

    /**
     * List of css files that may get included on the template file.
     * @var string[]
     */
    public $styles;

    /**
     * List of css code that may get included on the template file.
     * @var string[]
     */
    public $styles_code;

    /**
     * Template file that is going to get used by the renderer function.
     * @var string
     */
    public $template;

    /**
     * Path where template files reside.
     * @var string
     */
    public $templates_path;

    /**
     * Additional data that gets extracted and is accesible as variables
     * from the template file.
     * @var array
     */
    public $data;

    /**
     * List of menus accesible to template files.
     * @var array
     */
    public $menus;

    /**
     * Constructor.
     * @param string $url The url of currently viewed page.
     * @param string $templates_path Path where template files reside.
     */
    public function __construct(
        ServerRequestInterface $request, string $templates_path
    )
    {
        $this->request = $request;
        $this->scripts = array();
        $this->scripts_code = array();
        $this->styles = array();
        $this->styles_code = array();
        $this->template = "page.php";

        if(!is_dir($templates_path))
            throw new \ErrorException("Template directory does not exists.");

        $this->templates_path = rtrim($templates_path, "/");

        $this->data = array(
            "title" => "",
            "application_name",
            "content" => ""
        );

        return $this;
    }

    /**
     * Adds data that can be accessed from the template files as normal variables.
     * @param string $name
     * @param mixed $value
     * @return \Infocus\Web\View
     */
    public function addData(string $name, $value):View
    {
        $this->data[$name] = $value;

        return $this;
    }

    /**
     * Adds multiple data that can be accessed from the template files as
     * normal variables.
     * @param array $data Array in the form: ["name" => "value"]
     * @return \Infocus\Web\View
     */
    public function addBulkData(array $data):View
    {
        foreach($data as $name => $value)
        {
            $this->data[$name] = $value;
        }

        return $this;
    }

    /**
     * Popules the $scripts variable that can be accessed from template
     * files to render js links.
     * @param string $style_code
     * @return \Infocus\Web\View
     */
    public function addScript(string $script_file):View
    {
        $this->scripts[] = $script_file;

        return $this;
    }

    /**
     * Popules the $scripts_code variable that can be accessed from template
     * files to render js code.
     * @param string $style_code
     * @return \Infocus\Web\View
     */
    public function addScriptCode(string $script_code):View
    {
        $this->scripts_code[] = $script_code;

        return $this;
    }

    /**
     * Popules the $styles variable that can be accessed from template
     * files to render css links.
     * @param string $style_code
     * @return \Infocus\Web\View
     */
    public function addStyle(string $style_file):View
    {
        $this->styles[] = $style_file;

        return $this;
    }

    /**
     * Popules the $styles_code variable that can be accessed from template
     * files to render css styles.
     * @param string $style_code
     * @return \Infocus\Web\View
     */
    public function addStyleCode(string $style_code):View
    {
        $this->styles_code[] = $style_code;

        return $this;
    }

    /**
     * Adds an entry into the $menus array that can be accessed from template
     * files with the populated html file of the given items/links.
     * @param string $name Used as associative index ex: $menus[$name]
     * @param array $items Array in the format ["Label" => "path"]
     * @return \Infocus\Web\View
     */
    public function addMenu(string $name, array $items):View
    {
        $position = 1;
        $count_links = count($items);

        $links = "";

        if($count_links > 0)
        {
            $links .= "<ul class=\"menu $menu_name\">";

            foreach($items as $label => $link)
            {
                $list_class = "";

                if($position == 1)
                {
                    $list_class = " class=\"first l{$position}\"";
                }
                elseif($position == $count_links)
                {
                    $list_class = " class=\"last l{$position}\"";
                }
                else
                {
                    $list_class = " class=\"l{$position}\"";
                }

                $active = $this->getCurrentPath() == $link ?
                    "class=\"active\"" : ""
                ;

                $links .= "<li{$list_class}>"
                    . "<span $active>"
                    . "<a $active href=\"" . $this->url($link) . "\">"
                    . $label
                    . "</a>"
                    . "</span>"
                    . "</li>"
                ;

                $position++;
            }

            $links .= "</ul>";
        }

        $this->menus[$name] = $links;

        return $this;
    }

    /**
     * Sets the template file that is going to be rendered by default.
     * @param string $template
     * @return \Infocus\Web\View
     */
    public function setTemplate(string $template):View
    {
        if(!file_exists($this->templates_path . "/" . $template . ".php"))
            throw new \ErrorException("Could not find the template file.");

        $this->template = $template . ".php";

        return $this;
    }

    /**
     * Sets the $title variable that can be accessed from main template files
     * to display the title of the current content/section.
     * @param string $title
     * @return \Infocus\Web\View
     */
    public function setTitle(string $title):View
    {
        $this->addData("title", $title);
        return $this;
    }

    /**
     * Sets the $content variable that can be accessed from main template files
     * to display the main application content.
     * @param string $content
     * @return \Infocus\Web\View
     */
    public function setContent(string $content):View
    {
        $this->addData("content", $content);
        return $this;
    }

    /**
     * Sets the application name that is used to generate the $head_title
     * variable for the <title> tag.
     * @param string $name
     * @return \Infocus\Web\View
     */
    public function setApplicationName(string $name):View
    {
        $this->addData("application_name", $name);
        return $this;
    }

    /**
     * Renders the current view.
     * @param string $template Renders using another template.
     * @param array $additional_data Additional data for template file.
     * @return string
     */
    public function render(
        string $template = "", array $additional_data=array()
    ):string
    {
        if($template == "")
        {
            $template = $this->template;
        }
        else
        {
            if(stristr($template, ".php") === false)
            {
                $template .= ".php";
            }
        }

        extract($this->data);

        $additional_data += array(
            "head_title" => $this->data["title"]
                . " - "
                . $this->data["application_name"],
            "scripts" => $this->getScriptsHTML(),
            "scripts_code" => $this->getScriptsCodeHTML(),
            "styles" => $this->getStylesHTML(),
            "styles_code" => $this->getStylesCodeHTML(),
            "uri" => trim($this->request->getUri()->getPath(), "/"),
            "base_path" => $this->getBasePath(),
            "template_path" => $this->templates_path
        );

        extract($additional_data);

        $html = self::$pre_template_content;

        ob_start();
            include($this->templates_path . "/" . $template);
            $html = ob_get_contents();
        ob_end_clean();

        $html .= self::$post_template_content;

        return $html;
    }

    /**
     * Renders a template from a string instead of a file.
     * @param string $template Renders using another template.
     * @param array $additional_data Additional data for template file.
     * @return string
     */
    public function renderFromString(
        string $string, array $additional_data=array()
    ):string
    {
        extract($this->data);

        $additional_data += array(
            "head_title" => $this->data["title"]
                . " - "
                . $this->data["application_name"],
            "scripts" => $this->getScriptsHTML(),
            "scripts_code" => $this->getScriptsCodeHTML(),
            "styles" => $this->getStylesHTML(),
            "styles_code" => $this->getStylesCodeHTML(),
            "uri" => trim($this->request->getUri()->getPath(), "/"),
            "base_path" => $this->getBasePath(),
            "template_path" => $this->templates_path
        );

        extract($additional_data);

        $html = self::$pre_template_content;

        ob_start();
            eval("?>" . $string);
            $html = ob_get_contents();
        ob_end_clean();

        $html .= self::$post_template_content;

        return $html;
    }

    /**
     * Generates js links that can be included when rendering a template.
     * @return string
     */
    public function getScriptsHTML():string
    {
        $scripts_code = "";

        if(count($this->scripts) > 0)
        {
            foreach($this->scripts as $file)
            {
                $scripts_code .= '<script '
                    . 'type="text/javascript" '
                    . 'src="'.$this->getBasePath().$file.'">'
                    . '</script>'
                    . "\n"
                ;
            }
        }

        return $scripts_code;
    }

    /**
     * Generates js code that can be included when rendering a template.
     * @return string
     */
    public function getScriptsCodeHTML():string
    {
        $scripts_code = "";

        if(count($this->scripts_code) > 0)
        {
            foreach($this->scripts_code as $code)
            {
                $scripts_code .= '<script '
                    . 'type="text/javascript">'
                    . "\n"
                    . $code
                    . "\n"
                    . '</script>'
                    . "\n"
                ;
            }
        }

        return $scripts_code;
    }

    /**
     * Generates css links that can be included when rendering a template.
     * @return string
     */
    public function getStylesHTML():string
    {
        $styles_code = "";

        if(count($this->styles) > 0)
        {
            foreach($this->styles as $file)
            {
                $styles_code .= '<link '
                    . 'href="'.$this->getBasePath().$file.'" '
                    . 'rel="stylesheet" '
                    . 'type="text/css" '
                    . 'media="all"'
                    . '/>'
                    . "\n"
                ;
            }
        }

        return $styles_code;
    }

    /**
     * Generates css styles code that can be included when rendering a template.
     * @return string
     */
    public function getStylesCodeHTML():string
    {
        $styles_code = "";

        if(count($this->styles_code) > 0)
        {
            $styles_code .= '<style> ' . "\n";
            foreach($this->styles_code as $code)
            {
                $styles_code .= $code . "\n";
            }
            $styles_code .= '<style> ' . "\n";
        }

        return $styles_code;
    }

    /**
     * Generate a working url which can be used from within template files.
     * @param string $path
     * @param array $arguments
     * @return string
     */
    public function url(string $path, array $arguments = array()):string
    {
        $url = $this->getBasePath() . $path;

        if(count($arguments) > 0)
        {
            $formated_arguments = "?";

            foreach($arguments as $argument => $value)
            {
                if(!is_array($value) && "" . $value . "" != "")
                {
                    $formated_arguments .= $argument . "=" .
                        rawurlencode($value) . "&"
                    ;
                }
                elseif(is_array($value))
                {
                    foreach($value as $value_entry)
                    {
                        if("" . $value_entry . "" != "")
                        {
                            $formated_arguments .= $argument . "[]=" .
                                rawurlencode($value_entry) . "&"
                            ;
                        }
                    }
                }
            }

            $formated_arguments = rtrim($formated_arguments, "&");

            $url .= $formated_arguments;
        }

        return $url;
    }

    /**
     * Returns the base path of the site for proper url generation.
     * For example: http://test.com/site/section would return: /site/
     * Another example: http://test.com/section would return /
     * @return string
     */
    public function getBasePath():string
    {
        $paths = explode(
            "/",
            $this->request->getServerParams()["SCRIPT_NAME"]
        );

        unset($paths[count($paths) - 1]); //Remove [index].php

        $path = "/" . implode("/", $paths);

        if($path != "/")
        {
            $path .= "/";
        }

        return $path;
    }

    /**
     * Returns the base path of the site for proper url generation.
     * For example: http://test.com/site/section would return: /site/
     * Another example: http://test.com/section would return /
     * @return string
     */
    public function getCurrentPath():string
    {
        return trim($this->request->getUri()->getPath(), "/");
    }

    /**
     * Helper function to clean variables from within template files.
     * @param string $string
     * @param int $flags
     * @return string
     */
    public function escape($string, int $flags=0):string
    {
        return htmlspecialchars($string, $flags, 'UTF-8');
    }

    /**
     * Prepends content to all renderized views. Useful for debugging.
     * @param string $content
     * @return void
     */
    public static function prependContent(string $content):void
    {
        self::$pre_template_content .= $content;
    }

    /**
     * Appends content to all renderized views. Useful for debugging.
     * @param string $content
     * @return void
     */
    public static function appendContent($content):void
    {
        self::$post_template_content .= $content;
    }
}