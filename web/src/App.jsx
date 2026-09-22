import { useEffect, useState } from "react";
import Lenis from "lenis";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { SOCIAL } from "./data";
import Loader from "./components/Loader";
import Cursor from "./components/Cursor";
import Nav from "./components/Nav";
import Hero from "./components/Hero";
import Player from "./components/Player";
import Journey from "./components/Journey";
import Games from "./components/Games";
import Gameplay from "./components/Gameplay";
import OffGame from "./components/OffGame";
import Setup from "./components/Setup";
import Community from "./components/Community";
import Finale from "./components/Finale";

gsap.registerPlugin(ScrollTrigger);

export default function App() {
  const [progress, setProgress] = useState(0);
  const [ready, setReady] = useState(false);
  const [reduced, setReduced] = useState(false);

  useEffect(() => {
    const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    setReduced(reduce);

    const urls = [
      "/media/millz-portrait.png",
      "/media/millz.png",
      "/media/profile.jpg",
      "/media/logo-mark.png",
    ];
    let loaded = 0;
    urls.forEach((src) => {
      const img = new Image();
      img.onload = img.onerror = () => {
        loaded += 1;
        setProgress((loaded / urls.length) * 100);
        if (loaded === urls.length) setReady(true);
      };
      img.src = src;
    });

    if (reduce) return undefined;
    const lenis = new Lenis({ lerp: 0.08, smoothWheel: true });
    lenis.on("scroll", ScrollTrigger.update);
    const raf = (time) => {
      lenis.raf(time);
      requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);
    const onClick = (e) => {
      const a = e.target.closest('a[href^="#"]');
      if (!a) return;
      const id = a.getAttribute("href");
      const el = document.querySelector(id);
      if (el) {
        e.preventDefault();
        lenis.scrollTo(el, { offset: 0 });
      }
    };
    document.addEventListener("click", onClick);
    return () => {
      document.removeEventListener("click", onClick);
      lenis.destroy();
    };
  }, []);

  return (
    <div className={`site ${reduced ? "reduced" : ""}`}>
      <a className="skip" href="#player">Skip to content</a>
      <Loader progress={progress} ready={ready} />
      <Cursor />
      <Nav />
      <Hero reduced={reduced} />
      <Player />
      <Journey />
      <Games />
      <Gameplay />
      <OffGame />
      <Setup />
      <Community />
      <Finale />
      <footer className="site-footer">
        <span>© {new Date().getFullYear()} Young Millz</span>
        <span>
          <a href={SOCIAL.instagram} target="_blank" rel="noreferrer">Instagram ↗</a>
          {"  "}
          <a href={SOCIAL.tiktok} target="_blank" rel="noreferrer">TikTok ↗</a>
        </span>
      </footer>
    </div>
  );
}
