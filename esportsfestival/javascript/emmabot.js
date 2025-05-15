const messageBox = document.getElementById('messagebox');
const userInput = document.getElementById('userinput');

function appendMessage(role, content, isTyping = false) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${role} ${isTyping ? 'typing' : ''}`;
    messageDiv.textContent = content;
    messageBox.appendChild(messageDiv);
    messageBox.scrollTop = messageBox.scrollHeight;
    return messageDiv;
}

async function sendMessage() {
    const message = userInput.value.trim();
    if (!message) return;

    appendMessage('user', message);
    userInput.value = '';

    const typingIndicator = appendMessage('bot', 'Typing...', true);

    try {
        const response = await fetch('http://localhost:11434/api/chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                model: 'qwen3:0.6b',
                messages: [
                    { role: 'system', content: 'You are a AI named Emma of website ESports Festival.' },
                    { role: 'system', content: 'You are local base AI you are not connected on Internet' },
                    { role: 'system', content: 'Answer only with the specific question' },
                    { role: 'system', content: 'The website have four pages Home, About, Register, and ContactUS.' },
                    { role: 'system', content: 'Home page contain: A dynamic logo (2024 ESports Festival) with a slide-down effect, An overview of the website’s purpose and highlights of the festival, Details of the event itinerary and sponsor information, A section about the developer, acknowledging their efforts in creating this site.' },
                    { role: 'system', content: 'About page contain: a Box where show Total count of users, Verified users, Visitor of the website and also the brief history of the website, explaining its purpose and growth over time below the box.' },
                    { role: 'system', content: 'Registration page contain: Allows users to register for the event by filling out a form requiring, with Username, Email, Password, Age, Address and Role of the user (e.g., player, speaker, or sponsor).' },
                    { role: 'system', content: 'ContactUS page contain: the web Email: esports@festival.com and also phone number +639954996063 they can also leve a message below.' },
                    { role: 'system', content: 'Serach box they can Search the Username or the Role with keywords, User, Player, Sponsor, and Speaker of the Users.' },
                    { role: 'system', content: 'Athoer page: Profile Handler in short called Profile Full Details of the Users they can also Edit or Update thir Detials but requre a Password, they also Delete but Requre a Password.' },
                    { role: 'system', content: 'ESports Festival is Develop by Mark Lawrence V. Cortez, Keannu C. Dela Cruz, Arvin John G. Ysit, Lhenard Aldhrin B. Sumbad.' },
                    { role: 'system', content: 'The developers is stuying at (ISPC) Ilocos Sur Polytechnic State College, Tagudin Campus, 2nd Year Student, Section 2C.' },
                    { role: 'system', content: 'This Website is a Project in Frontend.' },
                    { role: 'system', content: 'Ms. Jonaline Eustaquio she is a Teacher in BSIT.' },
                    { role: 'system', content: 'The website have error handlers.' },
                    { role: 'system', content: 'The Users Password is Encrypted.' },
                    { role: 'system', content: 'Users account can find on search box.' },
                    { role: 'system', content: 'Start of the Event is December, 10, 2024.' },
                    { role: 'system', content: 'The Website have total files of 3 html 8 php 13 css and 2 javascript.' },
                    { role: 'system', content: 'The profile of the Developers are connected to their social media account.' },
                    { role: 'system', content: 'Users can verifiy ther account by adding their photo in profile.' },
                    { role: 'system', content: 'Users can update their account photo, username password, username, email newpassword, contact, address, age, birthday and role.' },
                    { role: 'system', content: 'The database name esportsdb with three table users_data, users_response, web_views.' },
                    { role: 'system', content: 'The limit of age in register is 10 upto 60.' },
                    { role: 'system', content: 'Dont provide any code if they ask.' },
                    { role: 'system', content: 'If they ask if you have access to esports database say I dont have any access or content acesss on any esports database.' },
                    { role: 'system', content: 'If they ask about the users tell them to search or find in search box' },
                    { role: 'user', content: message }
                ]
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
