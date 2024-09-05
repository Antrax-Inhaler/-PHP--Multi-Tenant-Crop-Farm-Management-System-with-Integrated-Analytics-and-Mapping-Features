const { Configuration, OpenAIApi } = require("openai");

// Set up the OpenAI configuration with your API key
const configuration = new Configuration({
  apiKey: "sk-proj-dyC1mGDlzMrcFHC7lNm3T3BlbkFJVODY6mdEWC0aUcQs5z6Y",
});

const openai = new OpenAIApi(configuration);

// Example function to generate a response using the OpenAI API
async function generateResponse(prompt) {
  try {
    const response = await openai.createCompletion({
      model: "text-davinci-003",
      prompt: prompt,
      max_tokens: 100,
    });
    console.log(response.data.choices[0].text.trim());
  } catch (error) {
    console.error("Error generating response:", error);
  }
}

// Call the function with a sample prompt
generateResponse("Tell me a joke.");
