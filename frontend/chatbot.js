const API_KEY = "AIzaSyBHcdaVsYYZT_4ZFGt5-xz6rdakI3oYceg";
const API_URL = `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=${API_KEY}`;

const chatBox = document.getElementById("chat-box");
const input = document.getElementById("message-input");
const sendBtn = document.getElementById("send-btn");

const conversationHistory = [
    {
        role: "user",
        parts: [{
            text: "Bạn là bác sĩ tư vấn sức khỏe. Trả lời ngắn gọn, dễ hiểu."
        }]
    }
];

function addMessage(text, sender) {
    const div = document.createElement("div");
    div.className = sender === "user" ? "user-message" : "bot-message";
    div.innerText = text;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

async function sendMessage() {
    const text = input.value.trim();
    if (!text) return;

    addMessage(text, "user");
    input.value = "";

    conversationHistory.push({
        role: "user",
        parts: [{ text }]
    });

    const loading = document.createElement("div");
    loading.className = "bot-message";
    loading.innerText = "⏳ Đang trả lời...";
    chatBox.appendChild(loading);
    chatBox.scrollTop = chatBox.scrollHeight;

    try {
        const response = await fetch(API_URL, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                contents: conversationHistory,
                generationConfig: {
                    temperature: 0.7,
                    maxOutputTokens: 1500
                }
            })
        });

        const data = await response.json();
        loading.remove();

        const reply = data.candidates?.[0]?.content?.parts?.[0]?.text || "Không có phản hồi";
        addMessage(reply, "bot");

        conversationHistory.push({
            role: "model",
            parts: [{ text: reply }]
        });

    } catch (err) {
        loading.remove();
        addMessage("❌ Lỗi kết nối API", "bot");
        console.error(err);
    }
}

sendBtn.addEventListener("click", sendMessage);
input.addEventListener("keydown", e => {
    if (e.key === "Enter") sendMessage();
});
