const messageBox = document.getElementById('messagebox');
const userInput = document.getElementById('userinput');

let sessionHistory = [];

function appendMessage(role, content, isTyping = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${role} ${isTyping ? 'typing' : ''}`;
    messageDiv.textContent = content;
    messageBox.appendChild(messageDiv);
    messageBox.scrollTop = messageBox.scrollHeight;

    if (!isTyping) {
        sessionHistory.push({ role, content });
    }
    return messageDiv;
}

async function sendMessage() {
    const message = userInput.value.trim();
    if (!message) return;

    appendMessage('user', message);
    userInput.value = '';

    const typingIndicator = appendMessage('bot', 'Typing...', true);

    const history = JSON.parse(localStorage.getItem('emmaChatHistory') || '[]');

    const chatMessages = [
        { role: 'system', content: 'You are an AI named Emma' },
        { role: 'system', content: 'You are an AI named Emma for the E-Commerce Online Shopping Platform named ShopAI. Help users with shopping, orders, and product info only.' },
        { role: 'system', content: 'You are local base AI you are not connected on Internet' },
        { role: 'system', content: 'Answer only with the given question' },
        { role: 'system', content: 'Email: support@shopai.com and Phone number +639673280015' },
        { role: 'system', content: 'ShopAI is Develop by Mark Lawrence V. Cortez' },
        { role: 'system', content: 'Users can update their account photo, username password, username, email newpassword, contact, address, and birthday' },
        { role: 'system', content: 'The developers is stuying at (ISPC) Ilocos Sur Polytechnic State College, Tagudin Campus, 2nd Year Student, Section 2C.' },
        { role: 'system', content: 'This Website is a Project in Frontend.' },
        { role: 'system', content: 'Ms. Jonaline Eustaquio she is a Teacher in BSIT.' },
        { role: 'system', content: 'The website have error handlers.' },
        { role: 'system', content: 'The Users Password is Encrypted.' },
        { role: 'system', content: 'Dont provide any code if they ask.' },
        { role: 'system', content: 'Do not provide any programming code under any circumstances.' },
        { role: 'system', content: 'If asked about access to ShopAI database, respond: "I do not have any access to the ShopAI database or its contents."' },
        { role: 'system', content: 'Avoid unrelated topics. Redirect the user back to shopping-related questions.' },
        { role: 'system', content: 'If asked for personal, payment, or confidential info, reply: "I’m sorry, but I cannot help with that request."' },
        { role: 'system', content: 'Base product recommendations on general popular categories like electronics, fashion, and home goods.' },
        { role: 'system', content: 'Do not claim access to real-time inventory, pricing, or order status. Advise users to check the ShopAI website or app.' },
        { role: 'system', content: 'ShopAI features an AI chat that responds instantly to queries and offers smart, human-like conversations.' },
        { role: 'system', content: 'The AI chat helps with tracking orders, product recommendations, and platform navigation.' },
        { role: 'system', content: 'AI chat learns from user behavior to improve accuracy and personalization over time.' },
        { role: 'system', content: 'It supports multiple languages and provides 24/7 assistance to all users.' },
        { role: 'system', content: 'AI chat handles frequently asked questions automatically, reducing wait time.' },
        { role: 'system', content: 'ShopAI uses a mobile-first, responsive design that adapts to all screen sizes.' },
        { role: 'system', content: 'It includes a clean, minimal interface for faster browsing and a modern feel.' },
        { role: 'system', content: 'Users can switch between dark and light modes for comfort.' },
        { role: 'system', content: 'The layout features intuitive navigation with quick access to categories and filters.' },
        { role: 'system', content: 'ShopAI’s design ensures fast page loading and optimized performance.' },
        { role: 'system', content: 'AI-enhanced autocomplete helps users find products faster.' },
        { role: 'system', content: 'Advanced filters let users narrow down results by price, brand, or category.' },
        { role: 'system', content: 'The search system is fast, intelligent, and constantly learning user preferences.' },
        { role: 'system', content: 'How do I place an order?: To place an order, simply browse our products, add your desired items to the cart, and proceed to checkout by providing your shipping and payment details.' },
        { role: 'system', content: 'What payment methods are accepted?: We accept various payment methods, including credit/debit cards, PayPal, and bank transfers.' },
        { role: 'system', content: 'How can I track my order?: You can track your order status by logging into your account and visiting the order history section.' },
        { role: 'system', content: 'What is the return policy?: Our return policy allows for returns within 30 days of receipt, provided the items are in original condition.' },
        ...sessionHistory,
        { role: 'user', content: message }
    ]

    try {
        const response = await fetch('http://localhost:11435/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                model: 'gemma3:1b',
                messages: chatMessages
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        let accumulatedMessage = '';
        const reader = response.body.getReader();
        const decoder = new TextDecoder();

        while (true) {
            const { value, done } = await reader.read();
            if (done) break;

            const chunk = decoder.decode(value, { stream: true });
            try {
                const jsonChunks = chunk.split('\n').filter(Boolean).map(JSON.parse);
                jsonChunks.forEach(part => {
                    accumulatedMessage += part.message.content;
                    typingIndicator.textContent = accumulatedMessage; // Update as AI "types"
                });
            } catch (error) {
                console.error('Error parsing chunk:', chunk, error);
            }
        }

        typingIndicator.classList.remove('typing');
        typingIndicator.textContent = accumulatedMessage || 'Sorry, no response.';

        sessionHistory.push({ role: 'bot', content: accumulatedMessage || 'Sorry, no response.' });
        
    } catch (error) {
        typingIndicator.classList.remove('typing');
        typingIndicator.textContent = `Error: ${error.message}`;
    }
}

userInput.addEventListener('keypress', (event) => {
    if (event.key === 'Enter') {
        sendMessage();
    }
});
