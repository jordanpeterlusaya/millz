import { useEffect, useRef } from "react";

export default function Cursor() {
  const ref = useRef(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (window.matchMedia("(pointer: coarse)").matches) {
      el.classList.add("hidden");
      return;
    }
    const move = (e) => {
      el.style.left = `${e.clientX}px`;
      el.style.top = `${e.clientY}px`;
    };
    const over = (e) => {
      const hit = e.target.closest("a, button, [data-cursor]");
      el.classList.toggle("hover", Boolean(hit));
      el.classList.toggle("view", hit?.dataset.cursor === "view");
    };
    window.addEventListener("pointermove", move);
    window.addEventListener("pointerover", over);
    return () => {
      window.removeEventListener("pointermove", move);
      window.removeEventListener("pointerover", over);
    };
  }, []);

  return <div className="cursor" ref={ref} />;
}
