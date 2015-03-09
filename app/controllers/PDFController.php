<?php

Dotenv::load(__DIR__ .'/../../');

class PDFController extends BaseController {
    
    public function create(){
        //PDF file is stored under project/public/download/info.pdf
        $Parsedown = new Parsedown();
        $parsed_note = $Parsedown->text(Input::get("note_text"));
        $pdf = PDF::loadHTML($parsed_note);
        $pdf->save("testtest.pdf");
        $file = public_path(). "/testtest.pdf";
        $headers = array(
              'Content-Type: application/pdf',
            );
        return Response::download($file, 'filename.pdf', $headers);
    }
}
