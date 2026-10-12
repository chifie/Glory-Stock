import { Link } from 'react-router-dom';

export default function CtaSection() {
  return (
    <section className="bg-slate-100 py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="overflow-hidden rounded-3xl bg-slate-900 px-6 py-14 text-center shadow-lg sm:px-16">
          <h2 className="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
            Take control of your stock today
          </h2>
          <p className="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-slate-300">
            Create a free account in under a minute. Register as staff for the counter,
            or as an admin to run the whole shop.
          </p>

          <div className="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <Link
              to="/register"
              className="w-full rounded-lg bg-blue-600 px-8 py-3 text-center text-base font-bold text-white transition hover:bg-blue-700 sm:w-auto"
            >
              Get Started Free
            </Link>
            <Link
              to="/login"
              className="w-full rounded-lg border border-slate-600 px-8 py-3 text-center text-base font-semibold text-slate-200 transition hover:bg-slate-800 sm:w-auto"
            >
              Login
            </Link>
          </div>
        </div>
      </div>
    </section>
  );
}
