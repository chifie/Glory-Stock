const steps = [
  {
    n: '1',
    title: 'Add your products',
    body: 'Create categories, then add each product with a SKU, price, and starting stock count.',
  },
  {
    n: '2',
    title: 'Sell at the counter',
    body: 'At the POS terminal, pick a product, enter a quantity, and confirm — stock and your audit trail update instantly.',
  },
  {
    n: '3',
    title: 'Check your numbers',
    body: 'Open the dashboard for value and profit, the reports page for six-month trends, and daily close for the end of day.',
  },
  {
    n: '4',
    title: 'Reorder in time',
    body: 'Low-stock items are flagged automatically. Print the reorder sheet and take it to your supplier.',
  },
];

export default function HowItWorks() {
  return (
    <section className="bg-white py-20">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="mx-auto max-w-2xl text-center">
          <h2 className="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">
            How it works
          </h2>
          <p className="mt-4 text-lg text-slate-600">
            Four steps from empty book to a fully tracked shop.
          </p>
        </div>

        <div className="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {steps.map((s) => (
            <div key={s.n} className="relative rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
              <span className="flex h-10 w-10 items-center justify-center rounded-full bg-blue-600 text-base font-extrabold text-white">
                {s.n}
              </span>
              <h3 className="mt-4 text-lg font-bold text-slate-900">{s.title}</h3>
              <p className="mt-2 text-sm leading-relaxed text-slate-600">{s.body}</p>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
}
