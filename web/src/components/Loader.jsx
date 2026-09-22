import { useEffect, useState } from "react";
import { SOCIAL } from "../data";

export default function Loader({ progress, ready, onDone }) {
  const [gone, setGone] = useState(false);

  useEffect(() => {
    if (!ready) return;
    const t = setTimeout(() => {
      setGone(true);
      onDone?.();
    }, 700);
    return () => clearTimeout(t);
  }, [ready, onDone]);

  return (
    <div className={`loader ${gone ? "gone" : ""}`} aria-hidden={gone}>
      <div>
        <div className="loader-kicker">Initializing</div>
        <h1>
          YOUNG MILLZ
          <br />
          EXPERIENCE
        </h1>
        <div className="loader-bar">
          <span style={{ width: `${Math.min(100, progress)}%` }} />
        </div>
        <div className="loader-pct">{String(Math.min(100, Math.round(progress))).padStart(3, "0")}</div>
        <p style={{ marginTop: 28, color: "#666", fontSize: 12, letterSpacing: ".18em" }}>
          <a href={SOCIAL.instagram} style={{ color: "#999" }}>Instagram</a>
          {"  ·  "}
          <a href={SOCIAL.tiktok} style={{ color: "#999" }}>TikTok</a>
        </p>
      </div>
    </div>
  );
}
