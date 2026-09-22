import { useEffect, useState } from "react";
import { SOCIAL } from "../data";
import HeroScene from "./HeroScene";

export default function Hero({ reduced }) {
  const [lite, setLite] = useState(true);
  const [mobile, setMobile] = useState(false);

  useEffect(() => {
    const isMobile = window.innerWidth < 768;
    setMobile(isMobile);
    setLite(reduced || isMobile);
  }, [reduced]);

  return (
    <section className="hero" id="home">
      {lite ? (
        <div className={`hero-fallback ${mobile ? "mobile" : ""}`} />
      ) : (
        <div className="hero-stage">
          <HeroScene reduced={false} />
        </div>
      )}

      <div className="hero-copy">
        <div className="hero-label">Official · Young Millz</div>
        <h1>
          <span>YOUNG</span>
          <span>MILLZ</span>
        </h1>
        <div className="hero-roles">
          <span>Gamer</span>
          <span>Creator</span>
          <span>Competitor</span>
        </div>
        <p className="hero-statement">
          A personal world built around the player — not a store, not a template.
          This is the digital home of Young Millz.
        </p>
        <div className="cta-row">
          <a className="btn" href="#player">Explore the journey</a>
          <a className="btn ghost" href={SOCIAL.tiktok} target="_blank" rel="noreferrer">Watch ↗</a>
          <a className="link-out" href={SOCIAL.instagram} target="_blank" rel="noreferrer">Follow Instagram ↗</a>
          <a className="link-out" href={SOCIAL.tiktok} target="_blank" rel="noreferrer">TikTok ↗</a>
        </div>
      </div>
    </section>
  );
}
