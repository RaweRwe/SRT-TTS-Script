<?php
// Replace with your own API key
$apiKey = 'YOUR_API_KEY';

// Read the contents of the file into a string
$srt_contents = file_get_contents('filename.srt');

// Regular expression to match the text of a segment
$text_pattern = '/^[0-9]+\n([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3} --> [0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3})\n(.*?)\n\n/s';

// Regular expression to match the start and end timestamps of a segment
$timestamps_pattern = '/^[0-9]+\n([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3}) --> ([0-9]{2}:[0-9]{2}:[0-9]{2},[0-9]{3})\n/';

// Initialize an array to store the segments
$segments = array();

// Use preg_match_all to match all segments in the SRT file
preg_match_all($text_pattern, $srt_contents, $matches);

// Iterate over each match
for ($i = 0; $i < count($matches[0]); $i++) {
    // Extract the text of the segment
    $text = $matches[2][$i];
  
    // Extract the timestamps
    preg_match($timestamps_pattern, $matches[1][$i], $timestamps);
    $start = strtotime($timestamps[1]);
    $end = strtotime($timestamps[2]);
    $duration = $end - $start;

    // Store the segment in the segments array
    $segments[] = array(
        'text' => $text,
        'start' => $start,
        'end' => $end,
        'duration' => $duration
    );
}

// Set the default language and voice options
$languageCode = 'en-US';
$ssmlGender = 'FEMALE';

// Check if a different language or voice has been specified
if (isset($_POST['languageCode'])) {
  $languageCode = $_POST['languageCode'];
}
if (isset($_POST['ssmlGender'])) {
  $ssmlGender = $_POST['ssmlGender'];
}

foreach ($segments as $segment) {
    $text = $segment['text'];
    $start = $segment['start'];
    $end = $segment['end'];
    $duration = $segment['duration'];
    // Generate audio for the segment using the Google Text-to-speech API
    $url = "https://texttospeech.googleapis.com/v1/text:synthesize?key=$apiKey";
    $data = [
      'input' => [
        'text' => $text,
      ],
      'voice' => [
        'languageCode' => $languageCode,
        'ssmlGender' => $ssmlGender,
      ],
      'audioConfig' => [
        'audioEncoding' => 'MP3',
      ],
    ];
    $options = [
      'http' => [
        'header'  => "Content-type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
      ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $responseData = json_decode($response, true);
    $audio = base64_decode($responseData['audioContent']);
  
    // Append silence to the audio to match the calculated duration
    $silenceDuration = $duration - strlen($audio) / 4 / 44.1; // 4 bytes per sample, 44.1 samples per second
    $silenceAudio = generateSilenceAudio($silenceDuration);
    $segmentAudio = $audio . $silenceAudio;
  
    // Concatenate the segment audio with the final audio
    $finalAudio = $finalAudio . $segmentAudio;
    }
    
    // Save the final audio to a file
    file_put_contents('final.mp3', $finalAudio);
    
    /**
    
    Generates silence audio of the specified duration.
    @param float $duration The duration of the silence audio in seconds
    @return string The generated silence audio
    
    */
    
  function generateSilenceAudio($duration) {
    $numSamples = $duration * 44.1; // 44.1 samples per second
    $data = '';
    for ($i = 0; $i < $numSamples; $i++) {
      $data .= pack('V', 0); // 4 bytes per sample
    }
    return $data;
  }
?>