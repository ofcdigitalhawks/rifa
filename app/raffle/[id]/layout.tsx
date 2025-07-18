import type React from "react"

export default function RaffleLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return <div className="bg-gray-100 min-h-screen">{children}</div>
}
