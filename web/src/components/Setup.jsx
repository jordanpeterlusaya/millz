export default function Setup() {
  return (
    <section className="setup" id="setup">
      <div className="kicker">Setup</div>
      <h2 className="display">The room.</h2>
      <p className="lede">
        Specifications stay blank until they are confirmed. What exists now is the space —
        light, screens, the posture of a session.
      </p>
      <div className="setup-frame">
        <img src="/media/banner-setup.jpg" alt="Young Millz gaming setup with dual controllers" />
        <button className="hotspot" style={{ left: "52%", top: "38%" }} data-label="Display" type="button" aria-label="Display" />
        <button className="hotspot" style={{ left: "44%", top: "72%" }} data-label="Controls" type="button" aria-label="Controls" />
        <button className="hotspot" style={{ left: "18%", top: "48%" }} data-label="Console" type="button" aria-label="Console" />
      </div>
    </section>
  );
}
