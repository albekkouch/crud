<?php
    include('lib/httpful.phar');

    //Récupère la liste de tous les documents
    function getAllDocuments() {
        $uri = "http://m2doc.kevingomez.fr/api/documents?_format=json";
        $response = \Httpful\Request::get($uri)->send();
        foreach($response->body->results as $doc) {
            echo $doc->id . '<br>';
        }
    }

    //Récupèré un document via son ID
    function getDocumentById($id) {
        $uri = 'http://m2doc.kevingomez.fr/api/documents/'.$id.'?_format=json';
        $response = \Httpful\Request::get($uri)->send();
        //Affichage du document récupéré
        echo $response->code. ' : Document trouvé<br><br>';
        switch ($response->code) {
            case 200:
                echo 'ID : ' . $response->body->id . '<br>';
                echo 'Titre : ' . $response->body->title . '<br>';
                echo 'Nom : ' . $response->body->file_name;
                break;
        }
    }

    //Fonctionne mais à éviter car nécessite cURL
    /*function createDocument($filePath) {
        $curl = "curl -i -F file=@" . realpath($filePath) . " http://m2doc.kevingomez.fr/api/documents";
        exec($curl, $output);
        foreach ($output as $value) {
            echo $value . "<br>";
        }
    }*/

    //Crée un document et l'affiche
    function createDocument($filePath) {
        //On teste si le fichier à envoyer existe
        if(file_exists($filePath)) {
            $uri = "http://m2doc.kevingomez.fr/api/documents";
            $file = array("file" => realpath($filePath));
            $response = \Httpful\Request::post($uri)
                ->sendsForm()
                ->attach($file)
                ->sendIt();

            //Affichage du code HTTP
            echo $response->code. ' : ';

            //Affichage du document créé
            switch ($response->code) {
                case 201:
                    echo 'Document correctement créé' . '<br><br>';
                    echo 'ID : '.$response->body->results->id . '<br>';
                    echo 'Titre : '.$response->body->results->title . '<br>';
                    echo 'Nom : '.$response->body->results->file_name;
                    break;
                case 409:
                    echo 'Ce fichier existe déjà';
                    break;
            }
        } else {
            echo 'Le document "'.$filePath.'" n\'existe pas';
        }
    }

    //Supprime un document via son ID
    function deleteDocumentById($id) {
        $uri = 'http://m2doc.kevingomez.fr/api/documents/'.$id;
        $response = \Httpful\Request::delete($uri)->send();
        //Retour
        echo $response->code. ' : ';
        switch($response->code) {
            case 204:
                echo 'Document supprimé';
                break;
            case 404:
                echo 'Le document n\'existe pas';
                break;
            default:
                echo 'Une erreur est survenue durant la suppression';
        }
    }