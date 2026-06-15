<?php
// Returns JSON array of suggested questions. Uses a simple local generator.
// Optionally integrate an external AI by setting env AI_API_KEY and enabling code below.
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tutor') { echo json_encode(['error'=>'Unauthorized']); exit(); }

$raw = file_get_contents('php://input');
$input = [];
if ($raw) {
    $dec = json_decode($raw, true);
    if (is_array($dec)) $input = $dec;
}
$subject = trim($input['subject'] ?? 'General');
$count = (int)($input['count'] ?? 5);
if ($count < 1) $count = 3; if ($count > 20) $count = 20;

// If OPENAI_API_KEY is configured, try to generate richer questions via OpenAI API.
$openai_key = trim(getenv('OPENAI_API_KEY') ?: '');
$questions = [];
if ($openai_key) {
    try {
        $prompt = "Generate $count quiz questions for the topic: $subject. Return JSON array of objects with fields: type ('mcq' or 'text'), question, choices (array of {label,is_correct}).";
        $post = [
            'model' => 'gpt-4o-mini',
            'messages' => [['role'=>'user','content'=>$prompt]],
            'max_tokens' => 800,
            'temperature' => 0.7
        ];
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $openai_key
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post));
        $resp = curl_exec($ch);
        if ($resp === false) throw new Exception('cURL error');
        $data = json_decode($resp, true);
        if (!empty($data['choices'][0]['message']['content'])){
            $content = $data['choices'][0]['message']['content'];
            // attempt to extract JSON from the content
            if (preg_match('/\{.*\}|\[.*\]/s', $content, $m)){
                $j = json_decode($m[0], true);
                if (is_array($j)){
                    // normalize
                    foreach ($j as $it) {
                        $it['temp_id'] = uniqid('q',true);
                        $questions[] = $it;
                    }
                }
            }
        }
    } catch (Exception $e) {
        // fall back to local generator below
        $questions = [];
    }
}

// fallback local generator
if (empty($questions)) {
    $questions = [];
    for ($i=0;$i<$count;$i++){
        $q = [];
        if ($i % 4 === 3) {
            $q['type'] = 'text';
            $q['question'] = "Short answer on: $subject (explain briefly)";
            $q['choices'] = [];
        } else {
            $q['type'] = 'mcq';
            $base = ($i+2);
            $correct = $base + 1;
            $q['question'] = "What is $base + 1?";
            $q['choices'] = [
                ['label'=>strval($base),'is_correct'=>0],
                ['label'=>strval($correct),'is_correct'=>1],
                ['label'=>strval($base+2),'is_correct'=>0]
            ];
        }
        $q['temp_id'] = uniqid('q',true);
        $questions[] = $q;
    }
}

echo json_encode(['questions'=>$questions]);
