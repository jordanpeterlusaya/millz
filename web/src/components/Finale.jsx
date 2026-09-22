import { SOCIAL } from "../data";

export default function Finale() {
  return (
    <section className="finale" id="connect">
      <div>
        <div className="kicker">Young Millz</div>
        <h2 className="display">See you in<br />the next game.</h2>
        <p>Follow · Watch · Connect</p>
        <div className="cta-row" style={{ justifyContent: "center" }}>
          <a className="btn" href={SOCIAL.instagram} target="_blank" rel="noreferrer">Instagram ↗</a>
          <a className="btn ghost" href={SOCIAL.tiktok} target="_blank" rel="noreferrer">TikTok ↗</a>
        </div>
      </div>
    </section>
  );
}
