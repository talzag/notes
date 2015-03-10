<?php

Dotenv::load(__DIR__ .'/../../');

class PDFController extends BaseController {
    
    public function create(){
        //PDF file is stored under project/public/download/info.pdf
        $note_text = Note::where("id",Input::get("id"))
            ->where("user_id",Auth::user()->id)
            ->first()
            ->note;
        $Parsedown = new Parsedown();
        $parsed_note = $Parsedown->text($note_text);
        $title = $this->findTitle($parsed_note);
        $header = '<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Blank Slate</title>
    <meta name="description" content="Get thoughts down quick, do things with them later. Just start typing">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, target-densityDpi=device-dpi" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/pdf.css">
</head>';
        $footer = "</body></html>";
        $note = $header.$parsed_note.$footer;
        $pdf = PDF::loadHTML($note);
        $pdf->save("testtest.pdf");
        $file= public_path(). "/testtest.pdf";
        $headers = array(
              'Content-Type: application/pdf',
            );
        return Response::download($file, $title.".pdf", $headers);
    }
    
	private function findTitle($text) {
    	$title = "";
        $html = str_get_html($text);
        $h1s = $html->find('h1');
        $h2s = $html->find('h2');
        if(count($h1s) > 0) {
            $title = $h1s[0]->innertext;
        } else if(count($h2s) > 0) {
            $title = $h2s[0]->innertext;
        } else {
            $title = $html->find("h3,p,h4,h5,li")[0]->innertext;
            if (strlen($title) > 60) {
                $title = substr($title, 0, 59).".....s";   
            }
        }
    	return $title;
	}
}
