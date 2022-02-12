<?php
/*
 * This file is part of PHPnv.
 *
 * (c) Heiler Nova <nvcode@outlook.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phpnv\Api;

use Exception;
use Throwable;

class ApiException extends Exception
{
    private array $messageDeveloper = [];
    private string $textBody = '';

    /**
     * @param (string|string[])[] $messages_developer
     * @param Throwable $th exection del cath
     */
    public function __construct(array $messages_developer, ?Throwable $th = null, $text_body = '', private $responeCode = 500)
    {
        $this->messageDeveloper = $messages_developer;
        $this->textBody = $text_body;
         
        parent::__construct('', 0, $th);
        if ($th){
            $this->message = $th->getMessage();
            $this->code = $th->getCode();
            $this->line = $th->getLine();
            $this->file = $th->getFile();
        }
    }

    /**
     * @return (string|string[])[]
     */
    public function getMessageDeveloper():array
    {
        return $this->messageDeveloper;
    }

    public function getResponseCode():int
    {
        return $this->responeCode;
    }
    public function getTextBody():string
    {
        return $this->textBody;
    }

    public function echo():void
    {
        header('content-type: text');
        if (Api::getConfig()->isDebug()){
            echo "Mensaje del desarrollador:\n";
            foreach($this->getMessageDeveloper() as $item){
                if (is_string($item)){
                    echo "$item\n";
                }else{
                    foreach($item as $sub_item){
                        echo "\t" . (is_string($sub_item) ? $sub_item : json_encode($sub_item, 128)) . "\n";
                    }
                }
            }
            echo "\n\nMessaje error: " . $this->getMessage() . "\n";
            echo "Code:    " . $this->getCode() . "\n";
            echo "File:    " . $this->getFile() . "\n";
            echo "Line:    " . $this->getLine() . "\n";
            echo "\n\n";
            echo "Rastro:";
            echo json_encode($this->getTrace(), 128);
        }else{
            echo "Error - api - server";
        }
        http_response_code($this->responeCode);
        exit;
    }
}