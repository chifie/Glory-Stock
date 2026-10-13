import LandingNavbar from '../components/landing/LandingNavbar';
import Hero from '../components/landing/Hero';
import Features from '../components/landing/Features';
import Benefits from '../components/landing/Benefits';
import HowItWorks from '../components/landing/HowItWorks';
import CtaSection from '../components/landing/CtaSection';
import Footer from '../components/landing/Footer';
import AIAssistant from '../components/landing/AIAssistant';

export default function Landing() {
  return (
    <div className="min-h-screen bg-white font-sans text-slate-900 antialiased">
      <LandingNavbar />
      <main>
        <Hero />
        <Features />
        <Benefits />
        <HowItWorks />
        <CtaSection />
      </main>
      <Footer />
      <AIAssistant />
    </div>
  );
}
