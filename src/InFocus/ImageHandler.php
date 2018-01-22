<?php
/**
 * @author Jefferson Gonzalez <jgmdev@gmail.com>
 * @license https://opensource.org/licenses/GPL-3.0
 * @link http://github.com/jgmdev/infocus Source code.
 */

namespace InFocus;

use \Slim\Http\Request;
use \Slim\Http\Response;

/**
 * Handler for the image/{name} scheme.
 */
class ImageHandler
{
    public function getResponse(Request $request, Response $response, array $args)
    {
        $activity = new \InFocus\Element\Activity();

        if($activity->loadFromBinaryName($args["name"]))
        {
            if($activity->icon_path != "")
            {
                if(preg_match("/.svg$/", $activity->icon_path))
                {
                    $response = $response->withAddedHeader(
                        "Content-Type", "image/svg+xml"
                    );

                    $svg = file_get_contents($activity->icon_path);

                    if(stristr($svg, "xmlns") === false)
                    {
                        $svg = str_replace(
                            "<svg ",
                            '<svg xmlns:dc="http://purl.org/dc/elements/1.1/" '
                            . 'xmlns:cc="http://creativecommons.org/ns#" '
                            . 'xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" '
                            . 'xmlns:svg="http://www.w3.org/2000/svg" '
                            . 'xmlns="http://www.w3.org/2000/svg" '
                            . 'xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" ',
                            $svg
                        );
                    }

                    if(
                        stristr(
                            $svg,
                            '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'
                        )
                        ===
                        false
                    )
                    {
                        $svg = str_replace(
                            "<svg ",
                            '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'."\n".'<svg ',
                            $svg
                        );
                    }

                    $response->getBody()->write(
                        $svg
                    );
                }
                else
                {
                    $response = $response->withAddedHeader(
                        "Content-Type", "image/png"
                    );

                    $response->getBody()->write(
                        file_get_contents($activity->icon_path)
                    );
                }

                return $response;
            }
        }

        $response = $response->withAddedHeader(
            "Content-Type", "image/svg+xml"
        );

        $svg = file_get_contents("static/images/window.svg");

        if(stristr($svg, "xmlns") === false)
        {
            $svg = str_replace(
                "<svg ",
                '<svg xmlns:dc="http://purl.org/dc/elements/1.1/" '
                . 'xmlns:cc="http://creativecommons.org/ns#" '
                . 'xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" '
                . 'xmlns:svg="http://www.w3.org/2000/svg" '
                . 'xmlns="http://www.w3.org/2000/svg" '
                . 'xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" ',
                $svg
            );
        }

        if(
            stristr(
                $svg,
                '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'
            )
            ===
            false
        )
        {
            $svg = str_replace(
                "<svg ",
                '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'."\n".'<svg ',
                $svg
            );
        }

        $response->getBody()->write(
            $svg
        );

        return $response;
    }
}