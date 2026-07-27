export interface ChatMessage {
  role: 'user' | 'assistant';
  content: string;
}

export class ChatWidget {
  private container: HTMLElement;
  private formElement: HTMLFormElement;
  private messagesDiv: HTMLElement;
  private inputField: HTMLInputElement;
  private sendBtn: HTMLButtonElement;
  private typingIndicator: HTMLElement;
  private conversation: ChatMessage[] = [];
  private formId: number;

  constructor(formElement: HTMLFormElement) {
    this.formElement = formElement;
    this.formId = Number(formElement.getAttribute('data-form-id') || 0);

    const wrapper = formElement.closest('.formsvox-form-wrapper') as HTMLElement;
    this.container = document.createElement('div');
    this.container.className = 'formsvox-ai-chat-box';

    this.container.innerHTML = `
      <div class="formsvox-ai-header">
        <span>FormsVox Assistant — Powered by VoiceCore AI</span>
      </div>
      <div class="formsvox-ai-messages" role="log" aria-live="polite" aria-label="Conversation log"></div>
      <div class="formsvox-ai-typing-indicator" style="display:none;" aria-hidden="true">
        <span></span><span></span><span></span>
      </div>
      <div class="formsvox-ai-input-wrap">
        <input type="text" class="formsvox-ai-input" placeholder="Type your message..." aria-label="Type message" />
        <button type="button" class="formsvox-ai-send-btn" aria-label="Send message">Send</button>
      </div>
      <div class="formsvox-ai-disclosure">
        AI Assistant — VoiceCore Data Privacy Disclosed
      </div>
    `;

    if (wrapper) {
      wrapper.appendChild(this.container);
    }

    this.messagesDiv = this.container.querySelector('.formsvox-ai-messages')!;
    this.inputField = this.container.querySelector('.formsvox-ai-input')!;
    this.sendBtn = this.container.querySelector('.formsvox-ai-send-btn')!;
    this.typingIndicator = this.container.querySelector('.formsvox-ai-typing-indicator')!;

    this.bindEvents();
    this.addAssistantMessage('Hello! I can help you complete this form conversationally. What is your name?');
  }

  private bindEvents() {
    this.sendBtn.addEventListener('click', () => this.handleUserSend());
    this.inputField.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') this.handleUserSend();
    });
  }

  public addUserMessage(text: string) {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'formsvox-ai-msg user';
    msgDiv.textContent = text;
    this.messagesDiv.appendChild(msgDiv);
    this.messagesDiv.scrollTop = this.messagesDiv.scrollHeight;
    this.conversation.push({ role: 'user', content: text });
  }

  public addAssistantMessage(text: string) {
    const msgDiv = document.createElement('div');
    msgDiv.className = 'formsvox-ai-msg assistant';
    msgDiv.textContent = text;
    this.messagesDiv.appendChild(msgDiv);
    this.messagesDiv.scrollTop = this.messagesDiv.scrollHeight;
    this.conversation.push({ role: 'assistant', content: text });
  }

  public showTyping(show: boolean) {
    this.typingIndicator.style.display = show ? 'flex' : 'none';
  }

  public fallbackToStandardForm(reason: string) {
    this.container.style.display = 'none';
    this.formElement.style.display = 'block';
    const notice = document.createElement('div');
    notice.className = 'formsvox-response-message info';
    notice.textContent = `${reason} Showing standard form below.`;
    this.formElement.insertBefore(notice, this.formElement.firstChild);
  }

  private async handleUserSend() {
    const text = this.inputField.value.trim();
    if (!text) return;
    this.inputField.value = '';
    this.addUserMessage(text);
    this.showTyping(true);

    try {
      const res = await fetch(`/wp-json/formsvox/v1/ai/chat`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          form_id: this.formId,
          messages: this.conversation,
        }),
      });

      this.showTyping(false);

      if (res.status === 429) {
        this.fallbackToStandardForm('AI monthly quota or rate limit reached.');
        return;
      }

      if (!res.ok) {
        this.fallbackToStandardForm('AI service connection error.');
        return;
      }

      const reader = res.body?.getReader();
      const decoder = new TextDecoder();
      let assistantText = '';

      if (reader) {
        while (true) {
          const { done, value } = await reader.read();
          if (done) break;
          const chunk = decoder.decode(value);
          const lines = chunk.split('\n\n');
          for (const line of lines) {
            if (line.startsWith('data: ')) {
              const rawData = line.replace('data: ', '').trim();
              if (rawData === '[DONE]') continue;
              try {
                const parsed = JSON.parse(rawData);
                if (parsed.type === 'text') {
                  assistantText += parsed.content;
                }
              } catch (e) {
                // Ignore parse errors
              }
            }
          }
        }
      }

      if (assistantText) {
        this.addAssistantMessage(assistantText);
      } else {
        this.addAssistantMessage('Thank you! Information noted.');
      }
    } catch (err) {
      this.showTyping(false);
      this.fallbackToStandardForm('Temporary network error.');
    }
  }
}
