const OpenAI = require('openai-api');

// Set your OpenAI API key
const apiKey = 'sk-proj-dyC1mGDlzMrcFHC7lNm3T3BlbkFJVODY6mdEWC0aUcQs5z6Y';
const openai = new OpenAI(apiKey);

// Define the user input prompt
const userInput = "Give me 3 ideas for apps I could build with OpenAI APIs";

// Send request to OpenAI Chat API
openai.complete({
    engine: "gpt-3.5-turbo",
    prompt: userInput,
    maxTokens: 150
}).then((response) => {
    // Extract and print the completion from the response
    const assistantReply = response.data.choices[0].text.trim();
    console.log(assistantReply);
}).catch((error) => {
    console.error('Error:', error);
});
