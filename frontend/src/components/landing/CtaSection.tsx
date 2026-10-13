import { Link } from 'react-router-dom';
import ScrollReveal from './ScrollReveal';
import { motion } from 'framer-motion';
import { ArrowRight, Sparkles } from 'lucide-react';

export default function CtaSection() {
  return (
    <section id="contact" className="relative overflow-hidden bg-slate-900 py-20 text-white">
      <div className="absolute inset-x-0 top-[-10%] h-72 bg-gradient-to-b from-blue-500/20 to-transparent blur-3xl" aria-hidden />
      <div className="absolute right-[-20%] top-20 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl" aria-hidden />
      <div className="absolute left-[-20%] top-40 h-96 w-96 rounded-full bg-emerald-500/10 blur-3xl" aria-hidden />

      <div className="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <ScrollReveal>
          <div className="mx-auto max-w-3xl text-center">
            <motion.span
              initial={{ opacity: 0, scale: 0.95 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ duration: 0.5 }}
              className="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-white backdrop-blur"
            >
              <Sparkles className="h-3.5 w-3.5" />
              Ready to get started
            </motion.span>
            <h2 className="mt-6 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl">
              Start managing your stock with confidence
            </h2>
            <p className="mt-4 text-lg leading-relaxed text-slate-300">
              Join shop owners who keep clean books, avoid stockouts, and always know their profit.
            </p>
            <motion.div
              initial={{ opacity: 0, y: 10 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ delay: 0.1, duration: 0.6 }}
              className="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row"
            >
              <Link
                to="/register"
                className="group inline-flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-500 px-8 py-3 text-center text-base font-bold text-white shadow-lg transition hover:shadow-xl sm:w-auto"
              >
                Get Started Free
                <ArrowRight className="h-4 w-4 transition-transform group-hover:translate-x-1" />
              </Link>
              <Link
                to="/login"
                className="w-full rounded-lg border border-white/20 bg-white/10 px-8 py-3 text-center text-base font-semibold text-white transition hover:bg-white/20 sm:w-auto"
              >
                Sign in to your account
              </Link>
            </motion.div>
          </div>
        </ScrollReveal>
      </div>
    </section>
  );
}
