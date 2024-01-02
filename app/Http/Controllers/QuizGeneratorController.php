<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use OpenAI;

class QuizGeneratorController extends Controller
{
    public function generateQuiz(Request $request)
    {

        // Validiamo l'input utente
        $validatedData = $request->validate([
            'categoria' => 'string',
            'argomento' => 'string|max:500',
            'difficoltà' => 'string',
        ]);

        // Creiamo il OpenAI client
        $client = OpenAI::client(env('OPENAI_API_KEY'));


        try {
            $result = $client->completions()->create([
                'model' => 'gpt-3.5-turbo-instruct',
                'prompt'=> "Genera un array JSON di 13 oggetti con domande di quiz di '{$validatedData['categoria']}', su '{$validatedData['argomento']}', a difficoltà '{$validatedData['difficoltà']}'. Ogni oggetto ha una chiave 'domanda', una chiave 'opzioni' con quattro risposte di cui una chiave 'rispostaCorretta' con la risposta corretta. Assicurati che le domande siano basate su fatti ben documentati. Evita ambiguità o informazioni errate. Fornisci risposte chiare e precise.",
                'temperature' => 0.2,
                'max_tokens' => 2000,
                'n' => 1, // Numero di risposte da generare
            ]);

            // Gestisci la risposta e restituisci al frontend
            return response()->json(['message' => 'Quiz generato con successo', 'quiz' => $result]);
        } catch (\Exception $e) {
            // Gestisci gli errori e restituisci al frontend
            return response()->json(['message' => 'Errore nella generazione del quiz', 'error' => $e->getMessage()], 500);
        }
    }
}
