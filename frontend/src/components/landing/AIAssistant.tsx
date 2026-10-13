import { useState, useRef, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Bot, Send, X, Sparkles } from 'lucide-react';

const suggestions = [
  'How do I track low stock?',
  'How to record a sale?',
  'What reports are available?',
  'How to add a product?',
];

const responses: Record<string, string> = {
  'How do I track low stock?': 'Go to Reorder page — items at/below threshold show as REORDER with a printable supplier sheet.',
  'How to record a sale?': 'Use POS — just pick a product and quantity, it updates stock and records the sale instantly.',
  'What reports are available?': 'Reports include Revenue vs Expenses, Profit, Best Sellers, and Daily Close summaries.',
  'How to add a product?': 'From Inventory/Products you can add SKU, name, price, category, initial stock, and reorder level.',
  'default': 'Ask me about sales, stock, low alerts, reports, or products.',
};

export default function AIAssistant() {
  const [open, setOpen] = useState(false);
  const [input, setInput] = useState('');
  const [messages, setMessages] = useState<Array<{ from: 'user' | 'ai'; text: string }>>([
    { from: 'ai', text: 'Hi! I can help you with GloryStock. What would you like to know?' },
  ]);
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    scrollRef.current?.scrollTo({ top: scrollRef.current.scrollHeight, behavior: 'smooth' });
  }, [messages, open]);

  const send = (text: string = input) => {
    const q = text.trim();
    if (!q) return;
    setMessages(m => [...m, { from: 'user', text: q }]);
    setInput('');
    setTimeout(() => {
      const ans = responses[q] || responses.default;
      setMessages(m => [...m, { from: 'ai', text: ans }]);
    }, 400);
  };

  return (
    <div className="fixed bottom-4 right-4 z-[60] sm:bottom-6 sm:right-6">
      <AnimatePresence>
        {open && (
          <motion.div
            initial={{ opacity: 0, y: 20, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 20, scale: 0.95 }}
            transition={{ duration: 0.25 }}
            className="mb-3 w-[calc(100vw-2rem)] max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
          >
            <div className="flex items-center justify-between border-b border-slate-200 bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 text-white">
              <div className="flex items-center gap-2">
                <div className="flex h-8 w-8 items-center justify-center rounded-full bg-white/20">
                  <Bot className="h-4 w-4" />
                </div>
                <div>
                  <p className="text-sm font-bold">GloryStock AI</p>
                  <p className="text-[10px] opacity-80">Always here to help</p>
                </div>
              </div>
              <button onClick={() => setOpen(false)} className="rounded-full p-1.5 hover:bg-white/20">
                <X className="h-4 w-4" />
              </button>
            </div>
            <div ref={scrollRef} className="max-h-80 overflow-y-auto p-4">
              <div className="space-y-2">
                {messages.map((msg, i) => (
                  <motion.div
                    key={i}
                    initial={{ opacity: 0, y: 4 }}
                    animate={{ opacity: 1, y: 0 }}
                    className={`flex ${msg.from === 'user' ? 'justify-end' : 'justify-start'}`}
                  >
                    <div
                      className={`max-w-[80%] rounded-2xl px-3 py-1.5 text-sm ${
                        msg.from === 'user'
                          ? 'rounded-br-sm bg-gradient-to-r from-blue-600 to-indigo-600 text-white'
                          : 'rounded-bl-sm border border-slate-200 bg-slate-50 text-slate-700'
                      }`}
                    >
                      {msg.text}
                    </div>
                  </motion.div>
                ))}
              </div>
              {messages.length === 1 && (
                <div className="mt-3 flex flex-wrap gap-2">
                  {suggestions.map(s => (
                    <button
                      key={s}
                      onClick={() => send(s)}
                      className="rounded-full border border-slate-200 px-2.5 py-1 text-[11px] text-slate-600 transition hover:bg-slate-50"
                    >
                      {s}
                    </button>
                  ))}
                </div>
              )}
            </div>
            <form
              onSubmit={e => {
                e.preventDefault();
                send();
              }}
              className="flex items-center gap-2 border-t border-slate-200 p-2"
            >
              <input
                value={input}
                onChange={e => setInput(e.target.value)}
                placeholder="Ask about GloryStock..."
                className="flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
              />
              <button
                type="submit"
                disabled={!input.trim()}
                className="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white disabled:opacity-50"
              >
                <Send className="h-4 w-4" />
              </button>
            </form>
          </motion.div>
        )}
      </AnimatePresence>
      <motion.button
        whileHover={{ scale: 1.05 }}
        whileTap={{ scale: 0.98 }}
        onClick={() => setOpen(o => !o)}
        className="group flex items-center gap-2 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-lg transition hover:shadow-xl"
        aria-label="Open AI assistant"
      >
        <Sparkles className="h-4 w-4" />
        <span className="hidden sm:inline">AI Assistant</span>
        <Bot className="sm:hidden" />
      </motion.button>
    </div>
  );
}
