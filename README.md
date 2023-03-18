# SRT-TTS-Script

How to use this?



Replace 'YOUR_API_KEY' in the first line with your own Google Text-to-speech API key.
Replace 'filename.srt' in the second line with the name of the SRT file that you want to convert to audio.
Optionally, you can specify the language and voice options by adding form fields to your HTML form with the names 'languageCode' and 'ssmlGender'. The default values are 'en-US' for language and 'FEMALE' for voice.
When the form is submitted, the script will generate an audio file called 'final.mp3' containing the spoken version of the SRT file. You can then play the audio file or do whatever else you want with it.



If need to explain:



The script first reads in the API key, which is required to use the Text-to-speech API. It then loads and parses an SRT file using the simplexml_load_file() function.



Next, the script sets default values for the language code and SSML (Speech Synthesis Markup Language) gender to use for the audio. It then checks if different values have been specified in the POST request, and updates the language code and SSML gender accordingly.



The script then iterates over each segment of text in the SRT file. For each segment, it calculates the start and end times, and the duration. It then makes a request to the Text-to-speech API to generate audio for the segment, using the specified language code and SSML gender. The generated audio is in MP3 format and is returned in the response from the API.



The script then calculates the amount of silence audio to append to the generated audio in order to match the calculated duration of the segment. It generates the silence audio using the generateSilenceAudio() function and concatenates it with the generated audio to form the final audio for the segment.



Finally, the script concatenates the final audio for all the segments and saves it to a file named 'final.mp3'. The generateSilenceAudio() function generates silence audio of the specified duration by creating an empty string and appending the specified number of 4-byte samples to it. The function returns the resulting string as the generated silence audio.

