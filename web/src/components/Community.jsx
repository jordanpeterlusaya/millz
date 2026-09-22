import { SOCIAL } from "../data";

export default function Community() {
  return (
    <section className="community" id="community">
      <div className="kicker">Community</div>
      <h2 className="display">Follow<br />the journey.</h2>
      <p className="lede" style={{ margin: "0 auto" }}>
        The only official profiles right now. If a new platform is added, it will appear here —
        not before.
      </p>
      <div className="social-block">
        <a className="social-card" href={SOCIAL.instagram} target="_blank" rel="noreferrer">
          <span>Instagram</span>
          <b>@young_millz05</b>
          <span>Open ↗</span>
        </a>
        <a className="social-card" href={SOCIAL.tiktok} target="_blank" rel="noreferrer">
          <span>TikTok</span>
          <b>@young_millz05</b>
          <span>Open ↗</span>
        </a>
      </div>
    </section>
  );
}
