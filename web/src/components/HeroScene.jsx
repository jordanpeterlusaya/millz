import { Canvas, useFrame } from "@react-three/fiber";
import { useTexture } from "@react-three/drei";
import { Suspense, useMemo, useRef } from "react";
import * as THREE from "three";

function Particles({ count = 700 }) {
  const ref = useRef();
  const positions = useMemo(() => {
    const arr = new Float32Array(count * 3);
    for (let i = 0; i < count; i += 1) {
      arr[i * 3] = (Math.random() - 0.5) * 14;
      arr[i * 3 + 1] = (Math.random() - 0.5) * 8;
      arr[i * 3 + 2] = (Math.random() - 0.7) * 10;
    }
    return arr;
  }, [count]);

  useFrame(({ clock }) => {
    if (ref.current) ref.current.rotation.y = clock.elapsedTime * 0.015;
  });

  return (
    <points ref={ref}>
      <bufferGeometry>
        <bufferAttribute attach="attributes-position" args={[positions, 3]} />
      </bufferGeometry>
      <pointsMaterial size={0.018} color="#d8ffe8" transparent opacity={0.55} depthWrite={false} />
    </points>
  );
}

function Portrait() {
  const tex = useTexture("/media/millz-portrait.png");
  tex.colorSpace = THREE.SRGBColorSpace;
  const ref = useRef();

  useFrame(({ pointer }) => {
    if (!ref.current) return;
    const x = pointer.x * 0.22;
    const y = pointer.y * 0.12;
    ref.current.rotation.y = THREE.MathUtils.lerp(ref.current.rotation.y, x, 0.045);
    ref.current.rotation.x = THREE.MathUtils.lerp(ref.current.rotation.x, -y, 0.045);
    ref.current.position.x = THREE.MathUtils.lerp(ref.current.position.x, 0.55 + x * 0.2, 0.05);
    ref.current.position.y = THREE.MathUtils.lerp(ref.current.position.y, y * 0.15, 0.05);
  });

  return (
    <mesh ref={ref} position={[0.55, -0.05, 0]}>
      <planeGeometry args={[3.15, 4.2, 24, 24]} />
      <meshStandardMaterial map={tex} transparent roughness={0.72} metalness={0.08} />
    </mesh>
  );
}

function Atmosphere() {
  const light = useRef();
  useFrame(({ pointer }) => {
    if (!light.current) return;
    light.current.position.x = THREE.MathUtils.lerp(light.current.position.x, pointer.x * 3, 0.04);
    light.current.position.y = THREE.MathUtils.lerp(light.current.position.y, 1 + pointer.y * 1.4, 0.04);
  });
  return (
    <>
      <color attach="background" args={["#080808"]} />
      <fog attach="fog" args={["#080808", 6, 16]} />
      <ambientLight intensity={0.28} />
      <pointLight ref={light} position={[2.2, 1.2, 3.4]} intensity={2.4} color="#9dffc6" distance={14} />
      <pointLight position={[-2.8, 0.2, 2.2]} intensity={0.7} color="#6b7cff" distance={10} />
      <directionalLight position={[-1.4, 1.6, 4]} intensity={0.55} />
    </>
  );
}

export default function HeroScene({ reduced }) {
  if (reduced) return null;
  const mobile = typeof window !== "undefined" && window.innerWidth < 768;

  return (
    <Canvas
      className="hero-canvas"
      camera={{ position: [0, 0, 5.2], fov: 42 }}
      dpr={[1, 1.6]}
      gl={{ antialias: true, alpha: false, powerPreference: "high-performance" }}
      onCreated={({ gl }) => gl.setClearColor("#080808")}
    >
      <Suspense fallback={null}>
        <Atmosphere />
        <Particles count={mobile ? 220 : 780} />
        <Portrait />
      </Suspense>
    </Canvas>
  );
}
