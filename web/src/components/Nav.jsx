import { useEffect, useState } from "react";
import { NAV, SOCIAL } from "../data";

export default function Nav() {
  const [solid, setSolid] = useState(false);
  const [open, setOpen] = useState(false);

  useEffect(() => {
    const onScroll = () => setSolid(window.scrollY > 40);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  return (
    <>
      <header className={`nav ${solid ? "solid" : ""}`}>
        <a className="brand" href="#home">
          <img src="/media/logo-mark.png" alt="Young Millz" />
          YOUNG MILLZ
        </a>
        <nav className="nav-links" aria-label="Primary">
          {NAV.map((item) => (
            <a key={item.id} href={`#${item.id}`}>{item.label}</a>
          ))}
        </nav>
        <a className="nav-cta" href={SOCIAL.instagram} target="_blank" rel="noreferrer">
          Follow ↗
        </a>
        <button className="menu-btn" type="button" onClick={() => setOpen((v) => !v)} aria-expanded={open}>
          {open ? "Close" : "Menu"}
        </button>
      </header>

      <div className={`mobile-menu ${open ? "open" : ""}`}>
        {NAV.map((item) => (
          <a key={item.id} href={`#${item.id}`} onClick={() => setOpen(false)}>{item.label}</a>
        ))}
        <a href={SOCIAL.instagram} target="_blank" rel="noreferrer">Instagram ↗</a>
        <a href={SOCIAL.tiktok} target="_blank" rel="noreferrer">TikTok ↗</a>
      </div>
    </>
  );
}
