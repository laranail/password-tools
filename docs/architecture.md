# Architecture

## Engine → key → locale

The engine emits hardcoded English prose. `ZxcvbnScorer` maps every string it can produce
onto the `FeedbackKey` enum — the one brittle spot, isolated in one class — and the shipped
catalogue translates the keys. Two guards make the pipeline safe to depend on: every enum
case must resolve in every shipped locale, and every string the VENDORED engine source can
emit must be mapped, so a bjeavons update that adds or rewords feedback is a red build, not
English leaking to a user.

## Why a contract

`PasswordScorer` keeps the engine an implementation detail: the rule, facade, command and
catalogue are written against `Score`, so swapping engines (or wrapping a different port)
touches one binding. The default binds `singletonIf` — an application binding always wins.

## The DoS cap

Scoring is CPU-bound on input length. The scorer reads at most the first 4096 characters —
beyond that, entropy is no longer the question being asked — so a pathological input costs
bounded time.

## Redaction

The password never appears in messages, logs, or command output. The check command takes no
argument at all: the value arrives through a hidden prompt so it cannot land in shell history
or the process list.

## Not in this package

Breach checking is `laranail/validation`'s `uncompromised()`; reuse prevention is
`laranail/password-history`; a client-side strength meter is deferred future scope (its
dictionaries are ~216 KB gzip — an opt-in lazy-loaded artifact if ever built, never a
`validation-js` dependency).

---

[← Docs index](../README.md#documentation)
