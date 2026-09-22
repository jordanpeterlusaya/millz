export default function Player() {
  return (
    <section className="player" id="player">
      <div className="player-photo">
        <img src="/media/profile.jpg" alt="Young Millz, photographed against a red backdrop" />
      </div>
      <div className="player-copy">
        <div className="kicker">The Player</div>
        <h2 className="display">Presence first.<br />Then the game.</h2>
        <p>
          Young Millz is a gamer and creator building a name through play, image, and
          consistency — the kind of presence that belongs to a person, not a product page.
        </p>
        <p>
          The headset. The shades. The silence before a match. Gaming is craft here:
          hours, taste, and a face the community can recognize.
        </p>
        <p className="lede">
          Who he is, what he plays, and how the world around him is assembled —
          that is the story this site exists to hold.
        </p>
      </div>
      <div className="player-strip">
        <img src="/media/millz.png" alt="Young Millz in headset among game artwork" />
      </div>
    </section>
  );
}
