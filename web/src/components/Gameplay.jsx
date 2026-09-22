import { SOCIAL } from "../data";

export default function Gameplay() {
  return (
    <section className="gameplay" id="gameplay">
      <img src="/media/games/subway-gameplay.png" alt="Subway Surfers gameplay still from Young Millz’s world" />
      <div className="gameplay-copy">
        <div className="kicker">Content</div>
        <h2 className="display">The reel<br />lives on TikTok.</h2>
        <div className="cta-row">
          <a className="btn" href={SOCIAL.tiktok} target="_blank" rel="noreferrer">Watch ↗</a>
          <a className="btn ghost" href={SOCIAL.instagram} target="_blank" rel="noreferrer">Latest on Instagram ↗</a>
        </div>
      </div>
    </section>
  );
}
