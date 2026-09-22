import { useState } from "react";
import { GAMES } from "../data";

export default function Games() {
  const [active, setActive] = useState(0);
  const game = GAMES[active];

  return (
    <section className="games" id="games">
      <div className="games-head">
        <div>
          <div className="kicker">The Games</div>
          <h2 className="display">Worlds he<br />moves through.</h2>
        </div>
        <p className="lede">
          Not a store. A map of the titles that sit in Young Millz’s visual world —
          from the posters behind him to the matches he plays.
        </p>
      </div>

      <div className="game-stage">
        <img className="wide" src={game.wide} alt="" />
        <div className="game-meta">
          <div className="kicker">Now in frame</div>
          <h3>{game.name}</h3>
          <p className="lede">{game.note}</p>
        </div>
        <div className="game-rail">
          {GAMES.map((g, i) => (
            <button
              key={g.name}
              className={`game-card ${i === active ? "active" : ""}`}
              onClick={() => setActive(i)}
              data-cursor="view"
              type="button"
            >
              <img src={g.image} alt={g.name} />
              <span>{g.name}</span>
            </button>
          ))}
        </div>
      </div>
    </section>
  );
}
