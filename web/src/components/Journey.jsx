export default function Journey() {
  const chapters = [
    {
      n: "01",
      title: "Origin",
      body: "The player before the brand. A face, a setup, a hunger to be known for how the game feels.",
    },
    {
      n: "02",
      title: "The craft",
      body: "eFootball, GTA, the hours that do not photograph. Identity formed in repetition.",
    },
    {
      n: "03",
      title: "The image",
      body: "Photography, fashion, the room. Young Millz as a person the camera can hold.",
    },
    {
      n: "04",
      title: "The world",
      body: "Community on Instagram and TikTok. The next chapter is still being written.",
    },
  ];

  return (
    <section className="journey" id="journey">
      <div className="journey-head">
        <div className="kicker">Career</div>
        <h2 className="display">The journey<br />is still live.</h2>
        <p className="lede" style={{ marginTop: 20 }}>
          Dates, titles and placements will sit here when they are confirmed.
          Nothing on this page is invented.
        </p>
      </div>
      <div className="chapters">
        {chapters.map((c) => (
          <article className="chapter" key={c.n}>
            <small>Chapter {c.n}</small>
            <h3>{c.title}</h3>
            <p>{c.body}</p>
            <div className="fill">Year / milestone — to be confirmed</div>
          </article>
        ))}
      </div>
    </section>
  );
}
