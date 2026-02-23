declare const wpApiSettings: {
  nonce?: string
  root?: string
  refreshNonce?: (nonce?: string) => Promise<string | null>
}
declare const wp: { customize?: any }

// allow raw-loader to work
declare module '*.css?raw' {
  const content: string
  export default content
}