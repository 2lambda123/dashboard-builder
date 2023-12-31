"use strict";
!function (e, n) {
    "object" == typeof exports && "undefined" != typeof module ? n(exports, require("d3-color")) : "function" == typeof define && define.amd ? define(["exports", "d3-color"], n) : n(e.d3_interpolate = e.d3_interpolate || {}, e.d3_color)
}(this, function (e, n) {
    function t(e) {
        return function () {
            return e
        }
    }

    function r(e, n) {
        return function (t) {
            return e + t * n
        }
    }

    function a(e, n, t) {
        return e = Math.pow(e, t), n = Math.pow(n, t) - e, t = 1 / t, function (r) {
            return Math.pow(e + r * n, t)
        }
    }

    function f(e, n) {
        var a = n - e;
        return a ? r(e, a > 180 || -180 > a ? a - 360 * Math.round(a / 360) : a) : t(isNaN(e) ? n : e)
    }

    function c(e) {
        return 1 === (e = +e) ? u : function (n, r) {
            return r - n ? a(n, r, e) : t(isNaN(n) ? r : n)
        }
    }

    function u(e, n) {
        var a = n - e;
        return a ? r(e, a) : t(isNaN(e) ? n : e)
    }

    function o(e, n) {
        var t, r = [], a = [], f = e ? e.length : 0, c = n ? n.length : 0, u = Math.min(f, c);
        for (t = 0; u > t; ++t) r.push(h(e[t], n[t]));
        for (; f > t; ++t) a[t] = e[t];
        for (; c > t; ++t) a[t] = n[t];
        return function (e) {
            for (t = 0; u > t; ++t) a[t] = r[t](e);
            return a
        }
    }

    function i(e, n) {
        return e = +e, n -= e, function (t) {
            return e + n * t
        }
    }

    function l(e, n) {
        var t, r = {}, a = {};
        null !== e && "object" == typeof e || (e = {}), null !== n && "object" == typeof n || (n = {});
        for (t in e) t in n ? r[t] = h(e[t], n[t]) : a[t] = e[t];
        for (t in n) t in e || (a[t] = n[t]);
        return function (e) {
            for (t in r) a[t] = r[t](e);
            return a
        }
    }

    function d(e) {
        return function () {
            return e
        }
    }

    function s(e) {
        return function (n) {
            return e(n) + ""
        }
    }

    function b(e, n) {
        var t, r, a, f = R.lastIndex = S.lastIndex = 0, c = -1, u = [], o = [];
        for (e += "", n += ""; (t = R.exec(e)) && (r = S.exec(n));) (a = r.index) > f && (a = n.slice(f, a), u[c] ? u[c] += a : u[++c] = a), (t = t[0]) === (r = r[0]) ? u[c] ? u[c] += r : u[++c] = r : (u[++c] = null, o.push({
            i: c,
            x: i(t, r)
        })), f = S.lastIndex;
        return f < n.length && (a = n.slice(f), u[c] ? u[c] += a : u[++c] = a), u.length < 2 ? o[0] ? s(o[0].x) : d(n) : (n = o.length, function (e) {
            for (var t, r = 0; n > r; ++r) u[(t = o[r]).i] = t.x(e);
            return u.join("")
        })
    }

    function h(e, r) {
        var a, f = typeof r;
        return null == r || "boolean" === f ? t(r) : ("number" === f ? i : "string" === f ? (a = n.color(r)) ? (r = a, E) : b : r instanceof n.color ? E : Array.isArray(r) ? o : l)(e, r)
    }

    function p(e, n) {
        return e = +e, n -= e, function (t) {
            return Math.round(e + n * t)
        }
    }

    function g(e, n, t, r, a, f) {
        if (e * r === n * t) return null;
        var c = Math.sqrt(e * e + n * n);
        e /= c, n /= c;
        var u = e * t + n * r;
        t -= e * u, r -= n * u;
        var o = Math.sqrt(t * t + r * r);
        return t /= o, r /= o, u /= o, n * t > e * r && (e = -e, n = -n, u = -u, c = -c), {
            translateX: a,
            translateY: f,
            rotate: Math.atan2(n, e) * C,
            skewX: Math.atan(u) * C,
            scaleX: c,
            scaleY: o
        }
    }

    function m(e) {
        if ("none" === e) return Y;
        P || (P = document.createElement("DIV"), j = document.documentElement, B = document.defaultView), P.style.transform = e, e = B.getComputedStyle(j.appendChild(P), null).getPropertyValue("transform"), j.removeChild(P);
        var n = e.slice(7, -1).split(",");
        return g(+n[0], +n[1], +n[2], +n[3], +n[4], +n[5])
    }

    function v(e) {
        q || (q = document.createElementNS("http://www.w3.org/2000/svg", "g")), q.setAttribute("transform", null == e ? "" : e);
        var n = q.transform.baseVal.consolidate().matrix;
        return g(n.a, n.b, n.c, n.d, n.e, n.f)
    }

    function y(e, n, t, r) {
        function a(e) {
            return e.length ? e.pop() + " " : ""
        }

        function f(e, r, a, f, c, u) {
            if (e !== a || r !== f) {
                var o = c.push("translate(", null, n, null, t);
                u.push({i: o - 4, x: i(e, a)}, {i: o - 2, x: i(r, f)})
            } else (a || f) && c.push("translate(" + a + n + f + t)
        }

        function c(e, n, t, f) {
            e !== n ? (e - n > 180 ? n += 360 : n - e > 180 && (e += 360), f.push({
                i: t.push(a(t) + "rotate(", null, r) - 2,
                x: i(e, n)
            })) : n && t.push(a(t) + "rotate(" + n + r)
        }

        function u(e, n, t, f) {
            e !== n ? f.push({
                i: t.push(a(t) + "skewX(", null, r) - 2,
                x: i(e, n)
            }) : n && t.push(a(t) + "skewX(" + n + r)
        }

        function o(e, n, t, r, f, c) {
            if (e !== t || n !== r) {
                var u = f.push(a(f) + "scale(", null, ",", null, ")");
                c.push({i: u - 4, x: i(e, t)}, {i: u - 2, x: i(n, r)})
            } else 1 === t && 1 === r || f.push(a(f) + "scale(" + t + "," + r + ")")
        }

        return function (n, t) {
            var r = [], a = [];
            return n = e(n), t = e(t), f(n.translateX, n.translateY, t.translateX, t.translateY, r, a), c(n.rotate, t.rotate, r, a), u(n.skewX, t.skewX, r, a), o(n.scaleX, n.scaleY, t.scaleX, t.scaleY, r, a), n = t = null, function (e) {
                for (var n, t = -1, f = a.length; ++t < f;) r[(n = a[t]).i] = n.x(e);
                return r.join("")
            }
        }
    }

    function w(e) {
        return ((e = Math.exp(e)) + 1 / e) / 2
    }

    function x(e) {
        return ((e = Math.exp(e)) - 1 / e) / 2
    }

    function M(e) {
        return ((e = Math.exp(2 * e)) - 1) / (e + 1)
    }

    function _(e, n) {
        var t, r, a = e[0], f = e[1], c = e[2], u = n[0], o = n[1], i = n[2], l = u - a, d = o - f, s = l * l + d * d;
        if (G > s) r = Math.log(i / c) / T, t = function (e) {
            return [a + e * l, f + e * d, c * Math.exp(T * e * r)]
        }; else {
            var b = Math.sqrt(s), h = (i * i - c * c + F * s) / (2 * c * z * b),
                p = (i * i - c * c - F * s) / (2 * i * z * b), g = Math.log(Math.sqrt(h * h + 1) - h),
                m = Math.log(Math.sqrt(p * p + 1) - p);
            r = (m - g) / T, t = function (e) {
                var n = e * r, t = w(g), u = c / (z * b) * (t * M(T * n + g) - x(g));
                return [a + u * l, f + u * d, c * t / w(T * n + g)]
            }
        }
        return t.duration = 1e3 * r, t
    }

    function k(e, t) {
        var r = f((e = n.hsl(e)).h, (t = n.hsl(t)).h), a = u(e.s, t.s), c = u(e.l, t.l), o = u(e.opacity, t.opacity);
        return function (n) {
            return e.h = r(n), e.s = a(n), e.l = c(n), e.opacity = o(n), e + ""
        }
    }

    function O(e, t) {
        var r = u((e = n.hsl(e)).h, (t = n.hsl(t)).h), a = u(e.s, t.s), f = u(e.l, t.l), c = u(e.opacity, t.opacity);
        return function (n) {
            return e.h = r(n), e.s = a(n), e.l = f(n), e.opacity = c(n), e + ""
        }
    }

    function N(e, t) {
        var r = u((e = n.lab(e)).l, (t = n.lab(t)).l), a = u(e.a, t.a), f = u(e.b, t.b), c = u(e.opacity, t.opacity);
        return function (n) {
            return e.l = r(n), e.a = a(n), e.b = f(n), e.opacity = c(n), e + ""
        }
    }

    function L(e, t) {
        var r = f((e = n.hcl(e)).h, (t = n.hcl(t)).h), a = u(e.c, t.c), c = u(e.l, t.l), o = u(e.opacity, t.opacity);
        return function (n) {
            return e.h = r(n), e.c = a(n), e.l = c(n), e.opacity = o(n), e + ""
        }
    }

    function A(e, t) {
        var r = u((e = n.hcl(e)).h, (t = n.hcl(t)).h), a = u(e.c, t.c), f = u(e.l, t.l), c = u(e.opacity, t.opacity);
        return function (n) {
            return e.h = r(n), e.c = a(n), e.l = f(n), e.opacity = c(n), e + ""
        }
    }

    var P, j, B, q, E = function V(e) {
            function t(e, t) {
                var a = r((e = n.rgb(e)).r, (t = n.rgb(t)).r), f = r(e.g, t.g), c = r(e.b, t.b),
                    u = r(e.opacity, t.opacity);
                return function (n) {
                    return e.r = a(n), e.g = f(n), e.b = c(n), e.opacity = u(n), e + ""
                }
            }

            var r = c(e);
            return t.gamma = V, t
        }(1), R = /[-+]?(?:\d+\.?\d*|\.?\d+)(?:[eE][-+]?\d+)?/g, S = new RegExp(R.source, "g"), C = 180 / Math.PI,
        Y = {translateX: 0, translateY: 0, rotate: 0, skewX: 0, scaleX: 1, scaleY: 1}, I = y(m, "px, ", "px)", "deg)"),
        X = y(v, ", ", ")", ")"), T = Math.SQRT2, z = 2, F = 4, G = 1e-12, $ = function Z(e) {
            function t(t, r) {
                var a = f((t = n.cubehelix(t)).h, (r = n.cubehelix(r)).h), c = u(t.s, r.s), o = u(t.l, r.l),
                    i = u(t.opacity, r.opacity);
                return function (n) {
                    return t.h = a(n), t.s = c(n), t.l = o(Math.pow(n, e)), t.opacity = i(n), t + ""
                }
            }

            return e = +e, t.gamma = Z, t
        }(1), D = function H(e) {
            function t(t, r) {
                var a = u((t = n.cubehelix(t)).h, (r = n.cubehelix(r)).h), f = u(t.s, r.s), c = u(t.l, r.l),
                    o = u(t.opacity, r.opacity);
                return function (n) {
                    return t.h = a(n), t.s = f(n), t.l = c(Math.pow(n, e)), t.opacity = o(n), t + ""
                }
            }

            return e = +e, t.gamma = H, t
        }(1), U = "0.7.0";
    e.version = U, e.interpolate = h, e.interpolateArray = o, e.interpolateNumber = i, e.interpolateObject = l, e.interpolateRound = p, e.interpolateString = b, e.interpolateTransformCss = I, e.interpolateTransformSvg = X, e.interpolateZoom = _, e.interpolateRgb = E, e.interpolateHsl = k, e.interpolateHslLong = O, e.interpolateLab = N, e.interpolateHcl = L, e.interpolateHclLong = A, e.interpolateCubehelix = $, e.interpolateCubehelixLong = D
}), !function (e, n) {
    "object" == typeof exports && "undefined" != typeof module ? n(exports, require("d3-array"), require("d3-collection"), require("d3-interpolate"), require("d3-format"), require("d3-time"), require("d3-time-format"), require("d3-color")) : "function" == typeof define && define.amd ? define(["exports", "d3-array", "d3-collection", "d3-interpolate", "d3-format", "d3-time", "d3-time-format", "d3-color"], n) : n(e.d3_scale = e.d3_scale || {}, e.d3_array, e.d3_collection, e.d3_interpolate, e.d3_format, e.d3_time, e.d3_time_format, e.d3_color)
}(this, function (e, n, t, r, a, f, c, u) {
    function o() {
        function e(e) {
            var t = e + "", c = n.get(t);
            if (!c) {
                if (f !== ue) return f;
                n.set(t, c = r.push(e))
            }
            return a[(c - 1) % a.length]
        }

        var n = t.map(), r = [], a = [], f = ue;
        return e.domain = function (a) {
            if (!arguments.length) return r.slice();
            r = [], n = t.map();
            for (var f, c, u = -1, o = a.length; ++u < o;) n.has(c = (f = a[u]) + "") || n.set(c, r.push(f));
            return e
        }, e.range = function (n) {
            return arguments.length ? (a = ce.call(n), e) : a.slice()
        }, e.unknown = function (n) {
            return arguments.length ? (f = n, e) : f
        }, e.copy = function () {
            return o().domain(r).range(a).unknown(f)
        }, e
    }

    function i() {
        function e() {
            var e = f().length, a = u[1] < u[0], o = u[a - 0], i = u[1 - a];
            t = (i - o) / Math.max(1, e - d + 2 * s), l && (t = Math.floor(t)), o += (i - o - t * (e - d)) * b, r = t * (1 - d), l && (o = Math.round(o), r = Math.round(r));
            var h = n.range(e).map(function (e) {
                return o + t * e
            });
            return c(a ? h.reverse() : h)
        }

        var t, r, a = o().unknown(void 0), f = a.domain, c = a.range, u = [0, 1], l = !1, d = 0, s = 0, b = .5;
        return delete a.unknown, a.domain = function (n) {
            return arguments.length ? (f(n), e()) : f()
        }, a.range = function (n) {
            return arguments.length ? (u = [+n[0], +n[1]], e()) : u.slice()
        }, a.rangeRound = function (n) {
            return u = [+n[0], +n[1]], l = !0, e()
        }, a.bandwidth = function () {
            return r
        }, a.step = function () {
            return t
        }, a.round = function (n) {
            return arguments.length ? (l = !!n, e()) : l
        }, a.padding = function (n) {
            return arguments.length ? (d = s = Math.max(0, Math.min(1, n)), e()) : d
        }, a.paddingInner = function (n) {
            return arguments.length ? (d = Math.max(0, Math.min(1, n)), e()) : d
        }, a.paddingOuter = function (n) {
            return arguments.length ? (s = Math.max(0, Math.min(1, n)), e()) : s
        }, a.align = function (n) {
            return arguments.length ? (b = Math.max(0, Math.min(1, n)), e()) : b
        }, a.copy = function () {
            return i().domain(f()).range(u).round(l).paddingInner(d).paddingOuter(s).align(b)
        }, e()
    }

    function l(e) {
        var n = e.copy;
        return e.padding = e.paddingOuter, delete e.paddingInner, delete e.paddingOuter, e.copy = function () {
            return l(n())
        }, e
    }

    function d() {
        return l(i().paddingInner(1))
    }

    function s(e) {
        return function () {
            return e
        }
    }

    function b(e) {
        return +e
    }

    function h(e, n) {
        return (n -= e = +e) ? function (t) {
            return (t - e) / n
        } : s(n)
    }

    function p(e) {
        return function (n, t) {
            var r = e(n = +n, t = +t);
            return function (e) {
                return n >= e ? 0 : e >= t ? 1 : r(e)
            }
        }
    }

    function g(e) {
        return function (n, t) {
            var r = e(n = +n, t = +t);
            return function (e) {
                return 0 >= e ? n : e >= 1 ? t : r(e)
            }
        }
    }

    function m(e, n, t, r) {
        var a = e[0], f = e[1], c = n[0], u = n[1];
        return a > f ? (a = t(f, a), c = r(u, c)) : (a = t(a, f), c = r(c, u)), function (e) {
            return c(a(e))
        }
    }

    function v(e, t, r, a) {
        var f = Math.min(e.length, t.length) - 1, c = new Array(f), u = new Array(f), o = -1;
        for (e[f] < e[0] && (e = e.slice().reverse(), t = t.slice().reverse()); ++o < f;) c[o] = r(e[o], e[o + 1]), u[o] = a(t[o], t[o + 1]);
        return function (t) {
            var r = n.bisect(e, t, 1, f) - 1;
            return u[r](c[r](t))
        }
    }

    function y(e, n) {
        return n.domain(e.domain()).range(e.range()).interpolate(e.interpolate()).clamp(e.clamp())
    }

    function w(e, n) {
        function t() {
            var t = Math.min(u.length, o.length) > 2 ? v : m;
            return f = t(u, o, l ? p(e) : e, i), c = t(o, u, h, l ? g(n) : n), a
        }

        function a(e) {
            return f(+e)
        }

        var f, c, u = oe, o = oe, i = r.interpolate, l = !1;
        return a.invert = function (e) {
            return c(+e)
        }, a.domain = function (e) {
            return arguments.length ? (u = fe.call(e, b), t()) : u.slice()
        }, a.range = function (e) {
            return arguments.length ? (o = ce.call(e), t()) : o.slice()
        }, a.rangeRound = function (e) {
            return o = ce.call(e), i = r.interpolateRound, t()
        }, a.clamp = function (e) {
            return arguments.length ? (l = !!e, t()) : l
        }, a.interpolate = function (e) {
            return arguments.length ? (i = e, t()) : i
        }, t()
    }

    function x(e, t, r) {
        var f, c = e[0], u = e[e.length - 1], o = n.tickStep(c, u, null == t ? 10 : t);
        switch (r = a.formatSpecifier(null == r ? ",f" : r), r.type) {
            case"s":
                var i = Math.max(Math.abs(c), Math.abs(u));
                return null != r.precision || isNaN(f = a.precisionPrefix(o, i)) || (r.precision = f), a.formatPrefix(r, i);
            case"":
            case"e":
            case"g":
            case"p":
            case"r":
                null != r.precision || isNaN(f = a.precisionRound(o, Math.max(Math.abs(c), Math.abs(u)))) || (r.precision = f - ("e" === r.type));
                break;
            case"f":
            case"%":
                null != r.precision || isNaN(f = a.precisionFixed(o)) || (r.precision = f - 2 * ("%" === r.type))
        }
        return a.format(r)
    }

    function M(e) {
        var t = e.domain;
        return e.ticks = function (e) {
            var r = t();
            return n.ticks(r[0], r[r.length - 1], null == e ? 10 : e)
        }, e.tickFormat = function (e, n) {
            return x(t(), e, n)
        }, e.nice = function (r) {
            var a = t(), f = a.length - 1, c = null == r ? 10 : r, u = a[0], o = a[f], i = n.tickStep(u, o, c);
            return i && (i = n.tickStep(Math.floor(u / i) * i, Math.ceil(o / i) * i, c), a[0] = Math.floor(u / i) * i, a[f] = Math.ceil(o / i) * i, t(a)), e
        }, e
    }

    function _() {
        var e = w(h, r.interpolateNumber);
        return e.copy = function () {
            return y(e, _())
        }, M(e)
    }

    function k() {
        function e(e) {
            return +e
        }

        var n = [0, 1];
        return e.invert = e, e.domain = e.range = function (t) {
            return arguments.length ? (n = fe.call(t, b), e) : n.slice()
        }, e.copy = function () {
            return k().domain(n)
        }, M(e)
    }

    function O(e, n) {
        e = e.slice();
        var t, r = 0, a = e.length - 1, f = e[r], c = e[a];
        return f > c && (t = r, r = a, a = t, t = f, f = c, c = t), e[r] = n.floor(f), e[a] = n.ceil(c), e
    }

    function N(e, n) {
        return (n = Math.log(n / e)) ? function (t) {
            return Math.log(t / e) / n
        } : s(n)
    }

    function L(e, n) {
        return 0 > e ? function (t) {
            return -Math.pow(-n, t) * Math.pow(-e, 1 - t)
        } : function (t) {
            return Math.pow(n, t) * Math.pow(e, 1 - t)
        }
    }

    function A(e) {
        return isFinite(e) ? +("1e" + e) : 0 > e ? 0 : e
    }

    function P(e) {
        return 10 === e ? A : e === Math.E ? Math.exp : function (n) {
            return Math.pow(e, n)
        }
    }

    function j(e) {
        return e === Math.E ? Math.log : 10 === e && Math.log10 || 2 === e && Math.log2 || (e = Math.log(e), function (n) {
            return Math.log(n) / e
        })
    }

    function B(e) {
        return function (n) {
            return -e(-n)
        }
    }

    function q() {
        function e() {
            return c = j(f), u = P(f), r()[0] < 0 && (c = B(c), u = B(u)), t
        }

        var t = w(N, L).domain([1, 10]), r = t.domain, f = 10, c = j(10), u = P(10);
        return t.base = function (n) {
            return arguments.length ? (f = +n, e()) : f
        }, t.domain = function (n) {
            return arguments.length ? (r(n), e()) : r()
        }, t.ticks = function (e) {
            var t, a = r(), o = a[0], i = a[a.length - 1];
            (t = o > i) && (b = o, o = i, i = b);
            var l, d, s, b = c(o), h = c(i), p = null == e ? 10 : +e, g = [];
            if (!(f % 1) && p > h - b) {
                if (b = Math.round(b) - 1, h = Math.round(h) + 1, o > 0) {
                    for (; h > b; ++b) for (d = 1, l = u(b); f > d; ++d) if (s = l * d, !(o > s)) {
                        if (s > i) break;
                        g.push(s)
                    }
                } else for (; h > b; ++b) for (d = f - 1, l = u(b); d >= 1; --d) if (s = l * d, !(o > s)) {
                    if (s > i) break;
                    g.push(s)
                }
                t && g.reverse()
            } else g = n.ticks(b, h, Math.min(h - b, p)).map(u);
            return g
        }, t.tickFormat = function (e, n) {
            if (null == n && (n = 10 === f ? ".0e" : ","), "function" != typeof n && (n = a.format(n)), e === 1 / 0) return n;
            null == e && (e = 10);
            var r = Math.max(1, f * e / t.ticks().length);
            return function (e) {
                var t = e / u(Math.round(c(e)));
                return f - .5 > t * f && (t *= f), r >= t ? n(e) : ""
            }
        }, t.nice = function () {
            return r(O(r(), {
                floor: function (e) {
                    return u(Math.floor(c(e)))
                }, ceil: function (e) {
                    return u(Math.ceil(c(e)))
                }
            }))
        }, t.copy = function () {
            return y(t, q().base(f))
        }, t
    }

    function E(e, n) {
        return 0 > e ? -Math.pow(-e, n) : Math.pow(e, n)
    }

    function R() {
        function e(e, n) {
            return (n = E(n, t) - (e = E(e, t))) ? function (r) {
                return (E(r, t) - e) / n
            } : s(n)
        }

        function n(e, n) {
            return n = E(n, t) - (e = E(e, t)), function (r) {
                return E(e + n * r, 1 / t)
            }
        }

        var t = 1, r = w(e, n), a = r.domain;
        return r.exponent = function (e) {
            return arguments.length ? (t = +e, a(a())) : t
        }, r.copy = function () {
            return y(r, R().exponent(t))
        }, M(r)
    }

    function S() {
        return R().exponent(.5)
    }

    function C() {
        function e() {
            var e = 0, c = Math.max(1, a.length);
            for (f = new Array(c - 1); ++e < c;) f[e - 1] = n.quantile(r, e / c);
            return t
        }

        function t(e) {
            return isNaN(e = +e) ? void 0 : a[n.bisect(f, e)]
        }

        var r = [], a = [], f = [];
        return t.invertExtent = function (e) {
            var n = a.indexOf(e);
            return 0 > n ? [0 / 0, 0 / 0] : [n > 0 ? f[n - 1] : r[0], n < f.length ? f[n] : r[r.length - 1]]
        }, t.domain = function (t) {
            if (!arguments.length) return r.slice();
            r = [];
            for (var a, f = 0, c = t.length; c > f; ++f) a = t[f], null == a || isNaN(a = +a) || r.push(a);
            return r.sort(n.ascending), e()
        }, t.range = function (n) {
            return arguments.length ? (a = ce.call(n), e()) : a.slice()
        }, t.quantiles = function () {
            return f.slice()
        }, t.copy = function () {
            return C().domain(r).range(a)
        }, t
    }

    function Y() {
        function e(e) {
            return e >= e ? u[n.bisect(c, e, 0, f)] : void 0
        }

        function t() {
            var n = -1;
            for (c = new Array(f); ++n < f;) c[n] = ((n + 1) * a - (n - f) * r) / (f + 1);
            return e
        }

        var r = 0, a = 1, f = 1, c = [.5], u = [0, 1];
        return e.domain = function (e) {
            return arguments.length ? (r = +e[0], a = +e[1], t()) : [r, a]
        }, e.range = function (e) {
            return arguments.length ? (f = (u = ce.call(e)).length - 1, t()) : u.slice()
        }, e.invertExtent = function (e) {
            var n = u.indexOf(e);
            return 0 > n ? [0 / 0, 0 / 0] : 1 > n ? [r, c[0]] : n >= f ? [c[f - 1], a] : [c[n - 1], c[n]]
        }, e.copy = function () {
            return Y().domain([r, a]).range(u)
        }, M(e)
    }

    function I() {
        function e(e) {
            return e >= e ? r[n.bisect(t, e, 0, a)] : void 0
        }

        var t = [.5], r = [0, 1], a = 1;
        return e.domain = function (n) {
            return arguments.length ? (t = ce.call(n), a = Math.min(t.length, r.length - 1), e) : t.slice()
        }, e.range = function (n) {
            return arguments.length ? (r = ce.call(n), a = Math.min(t.length, r.length - 1), e) : r.slice()
        }, e.invertExtent = function (e) {
            var n = r.indexOf(e);
            return [t[n - 1], t[n]]
        }, e.copy = function () {
            return I().domain(t).range(r)
        }, e
    }

    function X(e) {
        return new Date(e)
    }

    function T(e, t, a, f, c, u, o, i, l) {
        function d(n) {
            return (o(n) < n ? m : u(n) < n ? v : c(n) < n ? x : f(n) < n ? M : t(n) < n ? a(n) < n ? _ : k : e(n) < n ? N : L)(n)
        }

        function s(t, r, a, f) {
            if (null == t && (t = 10), "number" == typeof t) {
                var c = Math.abs(a - r) / t, u = n.bisector(function (e) {
                    return e[2]
                }).right(A, c);
                u === A.length ? (f = n.tickStep(r / pe, a / pe, t), t = e) : u ? (u = A[c / A[u - 1][2] < A[u][2] / c ? u - 1 : u], f = u[1], t = u[0]) : (f = n.tickStep(r, a, t), t = i)
            }
            return null == f ? t : t.every(f)
        }

        var b = w(h, r.interpolateNumber), p = b.invert, g = b.domain, m = l(".%L"), v = l(":%S"), x = l("%I:%M"),
            M = l("%I %p"), _ = l("%a %d"), k = l("%b %d"), N = l("%B"), L = l("%Y"),
            A = [[o, 1, ie], [o, 5, 5 * ie], [o, 15, 15 * ie], [o, 30, 30 * ie], [u, 1, le], [u, 5, 5 * le], [u, 15, 15 * le], [u, 30, 30 * le], [c, 1, de], [c, 3, 3 * de], [c, 6, 6 * de], [c, 12, 12 * de], [f, 1, se], [f, 2, 2 * se], [a, 1, be], [t, 1, he], [t, 3, 3 * he], [e, 1, pe]];
        return b.invert = function (e) {
            return new Date(p(e))
        }, b.domain = function (e) {
            return arguments.length ? g(e) : g().map(X)
        }, b.ticks = function (e, n) {
            var t, r = g(), a = r[0], f = r[r.length - 1], c = a > f;
            return c && (t = a, a = f, f = t), t = s(e, a, f, n), t = t ? t.range(a, f + 1) : [], c ? t.reverse() : t
        }, b.tickFormat = function (e) {
            return null == e ? d : l(e)
        }, b.nice = function (e, n) {
            var t = g();
            return (e = s(e, t[0], t[t.length - 1], n)) ? g(O(t, e)) : b
        }, b.copy = function () {
            return y(b, T(e, t, a, f, c, u, o, i, l))
        }, b
    }

    function z() {
        return T(f.timeYear, f.timeMonth, f.timeWeek, f.timeDay, f.timeHour, f.timeMinute, f.timeSecond, f.timeMillisecond, c.timeFormat).domain([new Date(2e3, 0, 1), new Date(2e3, 0, 2)])
    }

    function F() {
        return T(f.utcYear, f.utcMonth, f.utcWeek, f.utcDay, f.utcHour, f.utcMinute, f.utcSecond, f.utcMillisecond, c.utcFormat).domain([Date.UTC(2e3, 0, 1), Date.UTC(2e3, 0, 2)])
    }

    function G(e) {
        return e.match(/.{6}/g).map(function (e) {
            return "#" + e
        })
    }

    function $() {
        return o().range(ge)
    }

    function D() {
        return o().range(me)
    }

    function U() {
        return o().range(ve)
    }

    function V() {
        return o().range(ye)
    }

    function Z() {
        return _().interpolate(r.interpolateCubehelixLong).range([u.cubehelix(300, .5, 0), u.cubehelix(-240, .5, 1)])
    }

    function H(e) {
        function n(n) {
            var f = (n - t) / (r - t);
            return e(a ? Math.max(0, Math.min(1, f)) : f)
        }

        var t = 0, r = 1, a = !1;
        return n.domain = function (e) {
            return arguments.length ? (t = +e[0], r = +e[1], n) : [t, r]
        }, n.clamp = function (e) {
            return arguments.length ? (a = !!e, n) : a
        }, n.copy = function () {
            return H(e).domain([t, r]).clamp(a)
        }, M(n)
    }

    function K() {
        return H(r.interpolateCubehelixLong(u.cubehelix(-100, .75, .35), u.cubehelix(80, 1.5, .8)))
    }

    function Q() {
        return H(r.interpolateCubehelixLong(u.cubehelix(260, .75, .35), u.cubehelix(80, 1.5, .8)))
    }

    function W() {
        var e = u.cubehelix();
        return H(function (n) {
            (0 > n || n > 1) && (n -= Math.floor(n));
            var t = Math.abs(n - .5);
            return e.h = 360 * n - 100, e.s = 1.5 - 1.5 * t, e.l = .8 - .9 * t, e + ""
        })
    }

    function J(e) {
        var n = H(function (n) {
            return e[Math.round(n * e.length - n)]
        }).clamp(!0);
        return delete n.clamp, n
    }

    function ee() {
        return J(we)
    }

    function ne() {
        return J(xe)
    }

    function te() {
        return J(Me)
    }

    function re() {
        return J(_e)
    }

    var ae = Array.prototype, fe = ae.map, ce = ae.slice, ue = {name: "implicit"}, oe = [0, 1], ie = 1e3, le = 60 * ie,
        de = 60 * le, se = 24 * de, be = 7 * se, he = 30 * se, pe = 365 * se,
        ge = G("1f77b4ff7f0e2ca02cd627289467bd8c564be377c27f7f7fbcbd2217becf"),
        me = G("393b795254a36b6ecf9c9ede6379398ca252b5cf6bcedb9c8c6d31bd9e39e7ba52e7cb94843c39ad494ad6616be7969c7b4173a55194ce6dbdde9ed6"),
        ve = G("3182bd6baed69ecae1c6dbefe6550dfd8d3cfdae6bfdd0a231a35474c476a1d99bc7e9c0756bb19e9ac8bcbddcdadaeb636363969696bdbdbdd9d9d9"),
        ye = G("1f77b4aec7e8ff7f0effbb782ca02c98df8ad62728ff98969467bdc5b0d58c564bc49c94e377c2f7b6d27f7f7fc7c7c7bcbd22dbdb8d17becf9edae5"),
        we = G("44015444025645045745055946075a46085c460a5d460b5e470d60470e6147106347116447136548146748166848176948186a481a6c481b6d481c6e481d6f481f70482071482173482374482475482576482677482878482979472a7a472c7a472d7b472e7c472f7d46307e46327e46337f463480453581453781453882443983443a83443b84433d84433e85423f854240864241864142874144874045884046883f47883f48893e49893e4a893e4c8a3d4d8a3d4e8a3c4f8a3c508b3b518b3b528b3a538b3a548c39558c39568c38588c38598c375a8c375b8d365c8d365d8d355e8d355f8d34608d34618d33628d33638d32648e32658e31668e31678e31688e30698e306a8e2f6b8e2f6c8e2e6d8e2e6e8e2e6f8e2d708e2d718e2c718e2c728e2c738e2b748e2b758e2a768e2a778e2a788e29798e297a8e297b8e287c8e287d8e277e8e277f8e27808e26818e26828e26828e25838e25848e25858e24868e24878e23888e23898e238a8d228b8d228c8d228d8d218e8d218f8d21908d21918c20928c20928c20938c1f948c1f958b1f968b1f978b1f988b1f998a1f9a8a1e9b8a1e9c891e9d891f9e891f9f881fa0881fa1881fa1871fa28720a38620a48621a58521a68522a78522a88423a98324aa8325ab8225ac8226ad8127ad8128ae8029af7f2ab07f2cb17e2db27d2eb37c2fb47c31b57b32b67a34b67935b77937b87838b9773aba763bbb753dbc743fbc7340bd7242be7144bf7046c06f48c16e4ac16d4cc26c4ec36b50c46a52c56954c56856c66758c7655ac8645cc8635ec96260ca6063cb5f65cb5e67cc5c69cd5b6ccd5a6ece5870cf5773d05675d05477d1537ad1517cd2507fd34e81d34d84d44b86d54989d5488bd6468ed64590d74393d74195d84098d83e9bd93c9dd93ba0da39a2da37a5db36a8db34aadc32addc30b0dd2fb2dd2db5de2bb8de29bade28bddf26c0df25c2df23c5e021c8e020cae11fcde11dd0e11cd2e21bd5e21ad8e219dae319dde318dfe318e2e418e5e419e7e419eae51aece51befe51cf1e51df4e61ef6e620f8e621fbe723fde725"),
        xe = G("00000401000501010601010802010902020b02020d03030f03031204041405041606051806051a07061c08071e0907200a08220b09240c09260d0a290e0b2b100b2d110c2f120d31130d34140e36150e38160f3b180f3d19103f1a10421c10441d11471e114920114b21114e22115024125325125527125829115a2a115c2c115f2d11612f116331116533106734106936106b38106c390f6e3b0f703d0f713f0f72400f74420f75440f764510774710784910784a10794c117a4e117b4f127b51127c52137c54137d56147d57157e59157e5a167e5c167f5d177f5f187f601880621980641a80651a80671b80681c816a1c816b1d816d1d816e1e81701f81721f817320817521817621817822817922827b23827c23827e24828025828125818326818426818627818827818928818b29818c29818e2a81902a81912b81932b80942c80962c80982d80992d809b2e7f9c2e7f9e2f7fa02f7fa1307ea3307ea5317ea6317da8327daa337dab337cad347cae347bb0357bb2357bb3367ab5367ab73779b83779ba3878bc3978bd3977bf3a77c03a76c23b75c43c75c53c74c73d73c83e73ca3e72cc3f71cd4071cf4070d0416fd2426fd3436ed5446dd6456cd8456cd9466bdb476adc4869de4968df4a68e04c67e24d66e34e65e44f64e55064e75263e85362e95462ea5661eb5760ec5860ed5a5fee5b5eef5d5ef05f5ef1605df2625df2645cf3655cf4675cf4695cf56b5cf66c5cf66e5cf7705cf7725cf8745cf8765cf9785df9795df97b5dfa7d5efa7f5efa815ffb835ffb8560fb8761fc8961fc8a62fc8c63fc8e64fc9065fd9266fd9467fd9668fd9869fd9a6afd9b6bfe9d6cfe9f6dfea16efea36ffea571fea772fea973feaa74feac76feae77feb078feb27afeb47bfeb67cfeb77efeb97ffebb81febd82febf84fec185fec287fec488fec68afec88cfeca8dfecc8ffecd90fecf92fed194fed395fed597fed799fed89afdda9cfddc9efddea0fde0a1fde2a3fde3a5fde5a7fde7a9fde9aafdebacfcecaefceeb0fcf0b2fcf2b4fcf4b6fcf6b8fcf7b9fcf9bbfcfbbdfcfdbf"),
        Me = G("00000401000501010601010802010a02020c02020e03021004031204031405041706041907051b08051d09061f0a07220b07240c08260d08290e092b10092d110a30120a32140b34150b37160b39180c3c190c3e1b0c411c0c431e0c451f0c48210c4a230c4c240c4f260c51280b53290b552b0b572d0b592f0a5b310a5c320a5e340a5f3609613809623909633b09643d09653e0966400a67420a68440a68450a69470b6a490b6a4a0c6b4c0c6b4d0d6c4f0d6c510e6c520e6d540f6d550f6d57106e59106e5a116e5c126e5d126e5f136e61136e62146e64156e65156e67166e69166e6a176e6c186e6d186e6f196e71196e721a6e741a6e751b6e771c6d781c6d7a1d6d7c1d6d7d1e6d7f1e6c801f6c82206c84206b85216b87216b88226a8a226a8c23698d23698f24699025689225689326679526679727669827669a28659b29649d29649f2a63a02a63a22b62a32c61a52c60a62d60a82e5fa92e5eab2f5ead305dae305cb0315bb1325ab3325ab43359b63458b73557b93556ba3655bc3754bd3853bf3952c03a51c13a50c33b4fc43c4ec63d4dc73e4cc83f4bca404acb4149cc4248ce4347cf4446d04545d24644d34743d44842d54a41d74b3fd84c3ed94d3dda4e3cdb503bdd513ade5238df5337e05536e15635e25734e35933e45a31e55c30e65d2fe75e2ee8602de9612bea632aeb6429eb6628ec6726ed6925ee6a24ef6c23ef6e21f06f20f1711ff1731df2741cf3761bf37819f47918f57b17f57d15f67e14f68013f78212f78410f8850ff8870ef8890cf98b0bf98c0af98e09fa9008fa9207fa9407fb9606fb9706fb9906fb9b06fb9d07fc9f07fca108fca309fca50afca60cfca80dfcaa0ffcac11fcae12fcb014fcb216fcb418fbb61afbb81dfbba1ffbbc21fbbe23fac026fac228fac42afac62df9c72ff9c932f9cb35f8cd37f8cf3af7d13df7d340f6d543f6d746f5d949f5db4cf4dd4ff4df53f4e156f3e35af3e55df2e661f2e865f2ea69f1ec6df1ed71f1ef75f1f179f2f27df2f482f3f586f3f68af4f88ef5f992f6fa96f8fb9af9fc9dfafda1fcffa4"),
        _e = G("0d088710078813078916078a19068c1b068d1d068e20068f2206902406912605912805922a05932c05942e05952f059631059733059735049837049938049a3a049a3c049b3e049c3f049c41049d43039e44039e46039f48039f4903a04b03a14c02a14e02a25002a25102a35302a35502a45601a45801a45901a55b01a55c01a65e01a66001a66100a76300a76400a76600a76700a86900a86a00a86c00a86e00a86f00a87100a87201a87401a87501a87701a87801a87a02a87b02a87d03a87e03a88004a88104a78305a78405a78606a68707a68808a68a09a58b0aa58d0ba58e0ca48f0da4910ea3920fa39410a29511a19613a19814a099159f9a169f9c179e9d189d9e199da01a9ca11b9ba21d9aa31e9aa51f99a62098a72197a82296aa2395ab2494ac2694ad2793ae2892b02991b12a90b22b8fb32c8eb42e8db52f8cb6308bb7318ab83289ba3388bb3488bc3587bd3786be3885bf3984c03a83c13b82c23c81c33d80c43e7fc5407ec6417dc7427cc8437bc9447aca457acb4679cc4778cc4977cd4a76ce4b75cf4c74d04d73d14e72d24f71d35171d45270d5536fd5546ed6556dd7566cd8576bd9586ada5a6ada5b69db5c68dc5d67dd5e66de5f65de6164df6263e06363e16462e26561e26660e3685fe4695ee56a5de56b5de66c5ce76e5be76f5ae87059e97158e97257ea7457eb7556eb7655ec7754ed7953ed7a52ee7b51ef7c51ef7e50f07f4ff0804ef1814df1834cf2844bf3854bf3874af48849f48948f58b47f58c46f68d45f68f44f79044f79143f79342f89441f89540f9973ff9983ef99a3efa9b3dfa9c3cfa9e3bfb9f3afba139fba238fca338fca537fca636fca835fca934fdab33fdac33fdae32fdaf31fdb130fdb22ffdb42ffdb52efeb72dfeb82cfeba2cfebb2bfebd2afebe2afec029fdc229fdc328fdc527fdc627fdc827fdca26fdcb26fccd25fcce25fcd025fcd225fbd324fbd524fbd724fad824fada24f9dc24f9dd25f8df25f8e125f7e225f7e425f6e626f6e826f5e926f5eb27f4ed27f3ee27f3f027f2f227f1f426f1f525f0f724f0f921"),
        ke = "0.6.4";
    e.version = ke, e.scaleBand = i, e.scalePoint = d, e.scaleIdentity = k, e.scaleLinear = _, e.scaleLog = q, e.scaleOrdinal = o, e.scaleImplicit = ue, e.scalePow = R, e.scaleSqrt = S, e.scaleQuantile = C, e.scaleQuantize = Y, e.scaleThreshold = I, e.scaleTime = z, e.scaleUtc = F, e.scaleCategory10 = $, e.scaleCategory20b = D, e.scaleCategory20c = U, e.scaleCategory20 = V, e.scaleCubehelix = Z, e.scaleRainbow = W, e.scaleWarm = K, e.scaleCool = Q, e.scaleViridis = ee, e.scaleMagma = ne, e.scaleInferno = te, e.scalePlasma = re
}), function () {
    var e, n, t, r, a, f, c, u, o, i, l, d, s, b, h, p, g, m, v, y, w, x, M, _, k, O, N, L, A, P, j, B, q, E, R, S, C,
        Y, I, X, T, z, F, G, $, D, U, V, Z, H, K, Q, W, J, ee, ne, te, re, ae, fe, ce, ue, oe, ie, le, de, se, be, he,
        pe, ge, me, ve, ye, we, xe, Me, _e, ke, Oe = [].slice;
    we = function () {
        var e, n, t, r, a;
        for (e = {}, a = "Boolean Number String Function Array Date RegExp Undefined Null".split(" "), r = 0, n = a.length; n > r; r++) t = a[r], e["[object " + t + "]"] = t.toLowerCase();
        return function (n) {
            var t;
            return t = Object.prototype.toString.call(n), e[t] || "object"
        }
    }(), $ = function (e, n, t) {
        return null == n && (n = 0), null == t && (t = 1), n > e && (e = n), e > t && (e = t), e
    }, xe = function (e) {
        return e.length >= 3 ? [].slice.call(e) : e[0]
    }, y = function (e) {
        var n;
        for (n in e) 3 > n ? (e[n] < 0 && (e[n] = 0), e[n] > 255 && (e[n] = 255)) : 3 === n && (e[n] < 0 && (e[n] = 0), e[n] > 1 && (e[n] = 1));
        return e
    }, r = Math.PI, pe = Math.round, M = Math.cos, L = Math.floor, J = Math.pow, D = Math.log, me = Math.sin, ve = Math.sqrt, s = Math.atan2, Z = Math.max, d = Math.abs, c = 2 * r, a = r / 3, n = r / 180, f = 180 / r, v = function () {
        return arguments[0] instanceof e ? arguments[0] : function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, arguments, function () {
        })
    }, l = [], "undefined" != typeof module && null !== module && null != module.exports && (module.exports = v), "function" == typeof define && define.amd ? define([], function () {
        return v
    }) : (he = "undefined" != typeof exports && null !== exports ? exports : this, he.chroma = v), v.version = "1.1.1", i = {}, u = [], o = !1, e = function () {
        function e() {
            var e, n, t, r, a, f, c, l, d;
            for (f = this, n = [], l = 0, r = arguments.length; r > l; l++) e = arguments[l], null != e && n.push(e);
            if (c = n[n.length - 1], null != i[c]) f._rgb = y(i[c](xe(n.slice(0, -1)))); else {
                for (o || (u = u.sort(function (e, n) {
                    return n.p - e.p
                }), o = !0), d = 0, a = u.length; a > d && (t = u[d], !(c = t.test.apply(t, n))); d++) ;
                c && (f._rgb = y(i[c].apply(i, n)))
            }
            null == f._rgb && console.warn("unknown format: " + n), null == f._rgb && (f._rgb = [0, 0, 0]), 3 === f._rgb.length && f._rgb.push(1)
        }

        return e.prototype.alpha = function (e) {
            return arguments.length ? (this._rgb[3] = e, this) : this._rgb[3]
        }, e.prototype.toString = function () {
            return this.name()
        }, e
    }(), v._input = i, v.brewer = g = {
        OrRd: ["#fff7ec", "#fee8c8", "#fdd49e", "#fdbb84", "#fc8d59", "#ef6548", "#d7301f", "#b30000", "#7f0000"],
        PuBu: ["#fff7fb", "#ece7f2", "#d0d1e6", "#a6bddb", "#74a9cf", "#3690c0", "#0570b0", "#045a8d", "#023858"],
        BuPu: ["#f7fcfd", "#e0ecf4", "#bfd3e6", "#9ebcda", "#8c96c6", "#8c6bb1", "#88419d", "#810f7c", "#4d004b"],
        Oranges: ["#fff5eb", "#fee6ce", "#fdd0a2", "#fdae6b", "#fd8d3c", "#f16913", "#d94801", "#a63603", "#7f2704"],
        BuGn: ["#f7fcfd", "#e5f5f9", "#ccece6", "#99d8c9", "#66c2a4", "#41ae76", "#238b45", "#006d2c", "#00441b"],
        YlOrBr: ["#ffffe5", "#fff7bc", "#fee391", "#fec44f", "#fe9929", "#ec7014", "#cc4c02", "#993404", "#662506"],
        YlGn: ["#ffffe5", "#f7fcb9", "#d9f0a3", "#addd8e", "#78c679", "#41ab5d", "#238443", "#006837", "#004529"],
        Reds: ["#fff5f0", "#fee0d2", "#fcbba1", "#fc9272", "#fb6a4a", "#ef3b2c", "#cb181d", "#a50f15", "#67000d"],
        RdPu: ["#fff7f3", "#fde0dd", "#fcc5c0", "#fa9fb5", "#f768a1", "#dd3497", "#ae017e", "#7a0177", "#49006a"],
        Greens: ["#f7fcf5", "#e5f5e0", "#c7e9c0", "#a1d99b", "#74c476", "#41ab5d", "#238b45", "#006d2c", "#00441b"],
        YlGnBu: ["#ffffd9", "#edf8b1", "#c7e9b4", "#7fcdbb", "#41b6c4", "#1d91c0", "#225ea8", "#253494", "#081d58"],
        Purples: ["#fcfbfd", "#efedf5", "#dadaeb", "#bcbddc", "#9e9ac8", "#807dba", "#6a51a3", "#54278f", "#3f007d"],
        GnBu: ["#f7fcf0", "#e0f3db", "#ccebc5", "#a8ddb5", "#7bccc4", "#4eb3d3", "#2b8cbe", "#0868ac", "#084081"],
        Greys: ["#ffffff", "#f0f0f0", "#d9d9d9", "#bdbdbd", "#969696", "#737373", "#525252", "#252525", "#000000"],
        YlOrRd: ["#ffffcc", "#ffeda0", "#fed976", "#feb24c", "#fd8d3c", "#fc4e2a", "#e31a1c", "#bd0026", "#800026"],
        PuRd: ["#f7f4f9", "#e7e1ef", "#d4b9da", "#c994c7", "#df65b0", "#e7298a", "#ce1256", "#980043", "#67001f"],
        Blues: ["#f7fbff", "#deebf7", "#c6dbef", "#9ecae1", "#6baed6", "#4292c6", "#2171b5", "#08519c", "#08306b"],
        PuBuGn: ["#fff7fb", "#ece2f0", "#d0d1e6", "#a6bddb", "#67a9cf", "#3690c0", "#02818a", "#016c59", "#014636"],
        Spectral: ["#9e0142", "#d53e4f", "#f46d43", "#fdae61", "#fee08b", "#ffffbf", "#e6f598", "#abdda4", "#66c2a5", "#3288bd", "#5e4fa2"],
        RdYlGn: ["#a50026", "#d73027", "#f46d43", "#fdae61", "#fee08b", "#ffffbf", "#d9ef8b", "#a6d96a", "#66bd63", "#1a9850", "#006837"],
        RdBu: ["#67001f", "#b2182b", "#d6604d", "#f4a582", "#fddbc7", "#f7f7f7", "#d1e5f0", "#92c5de", "#4393c3", "#2166ac", "#053061"],
        PiYG: ["#8e0152", "#c51b7d", "#de77ae", "#f1b6da", "#fde0ef", "#f7f7f7", "#e6f5d0", "#b8e186", "#7fbc41", "#4d9221", "#276419"],
        PRGn: ["#40004b", "#762a83", "#9970ab", "#c2a5cf", "#e7d4e8", "#f7f7f7", "#d9f0d3", "#a6dba0", "#5aae61", "#1b7837", "#00441b"],
        RdYlBu: ["#a50026", "#d73027", "#f46d43", "#fdae61", "#fee090", "#ffffbf", "#e0f3f8", "#abd9e9", "#74add1", "#4575b4", "#313695"],
        BrBG: ["#543005", "#8c510a", "#bf812d", "#dfc27d", "#f6e8c3", "#f5f5f5", "#c7eae5", "#80cdc1", "#35978f", "#01665e", "#003c30"],
        RdGy: ["#67001f", "#b2182b", "#d6604d", "#f4a582", "#fddbc7", "#ffffff", "#e0e0e0", "#bababa", "#878787", "#4d4d4d", "#1a1a1a"],
        PuOr: ["#7f3b08", "#b35806", "#e08214", "#fdb863", "#fee0b6", "#f7f7f7", "#d8daeb", "#b2abd2", "#8073ac", "#542788", "#2d004b"],
        Set2: ["#66c2a5", "#fc8d62", "#8da0cb", "#e78ac3", "#a6d854", "#ffd92f", "#e5c494", "#b3b3b3"],
        Accent: ["#7fc97f", "#beaed4", "#fdc086", "#ffff99", "#386cb0", "#f0027f", "#bf5b17", "#666666"],
        Set1: ["#e41a1c", "#377eb8", "#4daf4a", "#984ea3", "#ff7f00", "#ffff33", "#a65628", "#f781bf", "#999999"],
        Set3: ["#8dd3c7", "#ffffb3", "#bebada", "#fb8072", "#80b1d3", "#fdb462", "#b3de69", "#fccde5", "#d9d9d9", "#bc80bd", "#ccebc5", "#ffed6f"],
        Dark2: ["#1b9e77", "#d95f02", "#7570b3", "#e7298a", "#66a61e", "#e6ab02", "#a6761d", "#666666"],
        Paired: ["#a6cee3", "#1f78b4", "#b2df8a", "#33a02c", "#fb9a99", "#e31a1c", "#fdbf6f", "#ff7f00", "#cab2d6", "#6a3d9a", "#ffff99", "#b15928"],
        Pastel2: ["#b3e2cd", "#fdcdac", "#cbd5e8", "#f4cae4", "#e6f5c9", "#fff2ae", "#f1e2cc", "#cccccc"],
        Pastel1: ["#fbb4ae", "#b3cde3", "#ccebc5", "#decbe4", "#fed9a6", "#ffffcc", "#e5d8bd", "#fddaec", "#f2f2f2"]
    }, Me = {
        indigo: "#4b0082",
        gold: "#ffd700",
        hotpink: "#ff69b4",
        firebrick: "#b22222",
        indianred: "#cd5c5c",
        yellow: "#ffff00",
        mistyrose: "#ffe4e1",
        darkolivegreen: "#556b2f",
        olive: "#808000",
        darkseagreen: "#8fbc8f",
        pink: "#ffc0cb",
        tomato: "#ff6347",
        lightcoral: "#f08080",
        orangered: "#ff4500",
        navajowhite: "#ffdead",
        lime: "#00ff00",
        palegreen: "#98fb98",
        darkslategrey: "#2f4f4f",
        greenyellow: "#adff2f",
        burlywood: "#deb887",
        seashell: "#fff5ee",
        mediumspringgreen: "#00fa9a",
        fuchsia: "#ff00ff",
        papayawhip: "#ffefd5",
        blanchedalmond: "#ffebcd",
        chartreuse: "#7fff00",
        dimgray: "#696969",
        black: "#000000",
        peachpuff: "#ffdab9",
        springgreen: "#00ff7f",
        aquamarine: "#7fffd4",
        white: "#ffffff",
        orange: "#ffa500",
        lightsalmon: "#ffa07a",
        darkslategray: "#2f4f4f",
        brown: "#a52a2a",
        ivory: "#fffff0",
        dodgerblue: "#1e90ff",
        peru: "#cd853f",
        lawngreen: "#7cfc00",
        chocolate: "#d2691e",
        crimson: "#dc143c",
        forestgreen: "#228b22",
        darkgrey: "#a9a9a9",
        lightseagreen: "#20b2aa",
        cyan: "#00ffff",
        mintcream: "#f5fffa",
        silver: "#c0c0c0",
        antiquewhite: "#faebd7",
        mediumorchid: "#ba55d3",
        skyblue: "#87ceeb",
        gray: "#808080",
        darkturquoise: "#00ced1",
        goldenrod: "#daa520",
        darkgreen: "#006400",
        floralwhite: "#fffaf0",
        darkviolet: "#9400d3",
        darkgray: "#a9a9a9",
        moccasin: "#ffe4b5",
        saddlebrown: "#8b4513",
        grey: "#808080",
        darkslateblue: "#483d8b",
        lightskyblue: "#87cefa",
        lightpink: "#ffb6c1",
        mediumvioletred: "#c71585",
        slategrey: "#708090",
        red: "#ff0000",
        deeppink: "#ff1493",
        limegreen: "#32cd32",
        darkmagenta: "#8b008b",
        palegoldenrod: "#eee8aa",
        plum: "#dda0dd",
        turquoise: "#40e0d0",
        lightgrey: "#d3d3d3",
        lightgoldenrodyellow: "#fafad2",
        darkgoldenrod: "#b8860b",
        lavender: "#e6e6fa",
        maroon: "#800000",
        yellowgreen: "#9acd32",
        sandybrown: "#f4a460",
        thistle: "#d8bfd8",
        violet: "#ee82ee",
        navy: "#000080",
        magenta: "#ff00ff",
        dimgrey: "#696969",
        tan: "#d2b48c",
        rosybrown: "#bc8f8f",
        olivedrab: "#6b8e23",
        blue: "#0000ff",
        lightblue: "#add8e6",
        ghostwhite: "#f8f8ff",
        honeydew: "#f0fff0",
        cornflowerblue: "#6495ed",
        slateblue: "#6a5acd",
        linen: "#faf0e6",
        darkblue: "#00008b",
        powderblue: "#b0e0e6",
        seagreen: "#2e8b57",
        darkkhaki: "#bdb76b",
        snow: "#fffafa",
        sienna: "#a0522d",
        mediumblue: "#0000cd",
        royalblue: "#4169e1",
        lightcyan: "#e0ffff",
        green: "#008000",
        mediumpurple: "#9370db",
        midnightblue: "#191970",
        cornsilk: "#fff8dc",
        paleturquoise: "#afeeee",
        bisque: "#ffe4c4",
        slategray: "#708090",
        darkcyan: "#008b8b",
        khaki: "#f0e68c",
        wheat: "#f5deb3",
        teal: "#008080",
        darkorchid: "#9932cc",
        deepskyblue: "#00bfff",
        salmon: "#fa8072",
        darkred: "#8b0000",
        steelblue: "#4682b4",
        palevioletred: "#db7093",
        lightslategray: "#778899",
        aliceblue: "#f0f8ff",
        lightslategrey: "#778899",
        lightgreen: "#90ee90",
        orchid: "#da70d6",
        gainsboro: "#dcdcdc",
        mediumseagreen: "#3cb371",
        lightgray: "#d3d3d3",
        mediumturquoise: "#48d1cc",
        lemonchiffon: "#fffacd",
        cadetblue: "#5f9ea0",
        lightyellow: "#ffffe0",
        lavenderblush: "#fff0f5",
        coral: "#ff7f50",
        purple: "#800080",
        aqua: "#00ffff",
        whitesmoke: "#f5f5f5",
        mediumslateblue: "#7b68ee",
        darkorange: "#ff8c00",
        mediumaquamarine: "#66cdaa",
        darksalmon: "#e9967a",
        beige: "#f5f5dc",
        blueviolet: "#8a2be2",
        azure: "#f0ffff",
        lightsteelblue: "#b0c4de",
        oldlace: "#fdf5e6",
        rebeccapurple: "#663399"
    }, v.colors = x = Me, X = function () {
        var e, n, r, a, f, c, u, o, i;
        return n = xe(arguments), f = n[0], e = n[1], r = n[2], o = (f + 16) / 116, u = isNaN(e) ? o : o + e / 500, i = isNaN(r) ? o : o - r / 200, o = t.Yn * T(o), u = t.Xn * T(u), i = t.Zn * T(i), c = ke(3.2404542 * u - 1.5371385 * o - .4985314 * i), a = ke(-.969266 * u + 1.8760108 * o + .041556 * i), r = ke(.0556434 * u - .2040259 * o + 1.0572252 * i), c = $(c, 0, 255), a = $(a, 0, 255), r = $(r, 0, 255), [c, a, r, n.length > 3 ? n[3] : 1]
    }, ke = function (e) {
        return pe(255 * (.00304 >= e ? 12.92 * e : 1.055 * J(e, 1 / 2.4) - .055))
    }, T = function (e) {
        return e > t.t1 ? e * e * e : t.t2 * (e - t.t0)
    }, t = {
        Kn: 18,
        Xn: .95047,
        Yn: 1,
        Zn: 1.08883,
        t0: .137931034,
        t1: .206896552,
        t2: .12841855,
        t3: .008856452
    }, ce = function () {
        var e, n, t, r, a, f, c, u;
        return r = xe(arguments), t = r[0], n = r[1], e = r[2], a = de(t, n, e), f = a[0], c = a[1], u = a[2], [116 * c - 16, 500 * (f - c), 200 * (c - u)]
    }, se = function (e) {
        return (e /= 255) <= .04045 ? e / 12.92 : J((e + .055) / 1.055, 2.4)
    }, _e = function (e) {
        return e > t.t3 ? J(e, 1 / 3) : e / t.t2 + t.t0
    }, de = function () {
        var e, n, r, a, f, c, u;
        return a = xe(arguments), r = a[0], n = a[1], e = a[2], r = se(r), n = se(n), e = se(e), f = _e((.4124564 * r + .3575761 * n + .1804375 * e) / t.Xn), c = _e((.2126729 * r + .7151522 * n + .072175 * e) / t.Yn), u = _e((.0193339 * r + .119192 * n + .9503041 * e) / t.Zn), [f, c, u]
    }, v.lab = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["lab"]), function () {
        })
    }, i.lab = X, e.prototype.lab = function () {
        return ce(this._rgb)
    }, b = function (e) {
        var n, t, r, a, f, c, u, o, i, l, d;
        return e = function () {
            var n, t, r;
            for (r = [], t = 0, n = e.length; n > t; t++) a = e[t], r.push(v(a));
            return r
        }(), 2 === e.length ? (i = function () {
            var n, t, r;
            for (r = [], t = 0, n = e.length; n > t; t++) a = e[t], r.push(a.lab());
            return r
        }(), f = i[0], c = i[1], n = function (e) {
            var n, t;
            return t = function () {
                var t, r;
                for (r = [], n = t = 0; 2 >= t; n = ++t) r.push(f[n] + e * (c[n] - f[n]));
                return r
            }(), v.lab.apply(v, t)
        }) : 3 === e.length ? (l = function () {
            var n, t, r;
            for (r = [], t = 0, n = e.length; n > t; t++) a = e[t], r.push(a.lab());
            return r
        }(), f = l[0], c = l[1], u = l[2], n = function (e) {
            var n, t;
            return t = function () {
                var t, r;
                for (r = [], n = t = 0; 2 >= t; n = ++t) r.push((1 - e) * (1 - e) * f[n] + 2 * (1 - e) * e * c[n] + e * e * u[n]);
                return r
            }(), v.lab.apply(v, t)
        }) : 4 === e.length ? (d = function () {
            var n, t, r;
            for (r = [], t = 0, n = e.length; n > t; t++) a = e[t], r.push(a.lab());
            return r
        }(), f = d[0], c = d[1], u = d[2], o = d[3], n = function (e) {
            var n, t;
            return t = function () {
                var t, r;
                for (r = [], n = t = 0; 2 >= t; n = ++t) r.push((1 - e) * (1 - e) * (1 - e) * f[n] + 3 * (1 - e) * (1 - e) * e * c[n] + 3 * (1 - e) * e * e * u[n] + e * e * e * o[n]);
                return r
            }(), v.lab.apply(v, t)
        }) : 5 === e.length && (t = b(e.slice(0, 3)), r = b(e.slice(2, 5)), n = function (e) {
            return .5 > e ? t(2 * e) : r(2 * (e - .5))
        }), n
    }, v.bezier = function (e) {
        var n;
        return n = b(e), n.scale = function () {
            return v.scale(n)
        }, n
    }, v.cubehelix = function (e, n, t, r, a) {
        var f, u, o;
        return null == e && (e = 300), null == n && (n = -1.5), null == t && (t = 1), null == r && (r = 1), null == a && (a = [0, 1]), u = a[1] - a[0], f = 0, o = function (o) {
            var i, l, d, s, b, h, p, g, m;
            return i = c * ((e + 120) / 360 + n * o), p = J(a[0] + u * o, r), h = 0 !== f ? t[0] + o * f : t, l = h * p * (1 - p) / 2, s = M(i), m = me(i), g = p + l * (-.14861 * s + 1.78277 * m), b = p + l * (-.29227 * s - .90649 * m), d = p + 1.97294 * l * s, v(y([255 * g, 255 * b, 255 * d]))
        }, o.start = function (n) {
            return null == n ? e : (e = n, o)
        }, o.rotations = function (e) {
            return null == e ? n : (n = e, o)
        }, o.gamma = function (e) {
            return null == e ? r : (r = e, o)
        }, o.hue = function (e) {
            return null == e ? t : (t = e, "array" === we(t) ? (f = t[1] - t[0], 0 === f && (t = t[1])) : f = 0, o)
        }, o.lightness = function (e) {
            return null == e ? a : (a = e, "array" === we(a) ? (u = a[1] - a[0], 0 === u && (a = a[1])) : u = 0, o)
        }, o.scale = function () {
            return v.scale(o)
        }, o.hue(t), o
    }, v.random = function () {
        var n, t, r, a;
        for (t = "0123456789abcdef", n = "#", r = a = 0; 6 > a; r = ++a) n += t.charAt(L(16 * Math.random()));
        return new e(n)
    }, i.rgb = function () {
        var e, n, t, r;
        n = xe(arguments), t = [];
        for (e in n) r = n[e], t.push(r);
        return t
    }, v.rgb = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["rgb"]), function () {
        })
    }, e.prototype.rgb = function () {
        return this._rgb.slice(0, 3)
    }, e.prototype.rgba = function () {
        return this._rgb
    }, u.push({
        p: 15, test: function () {
            var e;
            return e = xe(arguments), "array" === we(e) && 3 === e.length ? "rgb" : 4 === e.length && "number" === we(e[3]) && e[3] >= 0 && e[3] <= 1 ? "rgb" : void 0
        }
    }), A = function (e) {
        var n, t, r, a, f, c;
        if (e.match(/^#?([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/)) return (4 === e.length || 7 === e.length) && (e = e.substr(1)), 3 === e.length && (e = e.split(""), e = e[0] + e[0] + e[1] + e[1] + e[2] + e[2]), c = parseInt(e, 16), a = c >> 16, r = c >> 8 & 255, t = 255 & c, [a, r, t, 1];
        if (e.match(/^#?([A-Fa-f0-9]{8})$/)) return 9 === e.length && (e = e.substr(1)), c = parseInt(e, 16), a = c >> 24 & 255, r = c >> 16 & 255, t = c >> 8 & 255, n = pe((255 & c) / 255 * 100) / 100, [a, r, t, n];
        if (null != i.css && (f = i.css(e))) return f;
        throw"unknown color: " + e
    }, te = function (e, n) {
        var t, r, a, f, c, u, o;
        return null == n && (n = "rgb"), c = e[0], a = e[1], r = e[2], t = e[3], o = c << 16 | a << 8 | r, u = "000000" + o.toString(16), u = u.substr(u.length - 6), f = "0" + pe(255 * t).toString(16), f = f.substr(f.length - 2), "#" + function () {
            switch (n.toLowerCase()) {
                case"rgba":
                    return u + f;
                case"argb":
                    return f + u;
                default:
                    return u
            }
        }()
    }, i.hex = function (e) {
        return A(e)
    }, v.hex = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["hex"]), function () {
        })
    }, e.prototype.hex = function (e) {
        return null == e && (e = "rgb"), te(this._rgb, e)
    }, u.push({
        p: 10, test: function (e) {
            return 1 === arguments.length && "string" === we(e) ? "hex" : void 0
        }
    }), B = function () {
        var e, n, t, r, a, f, c, u, o, i, l, d, s, b;
        if (e = xe(arguments), a = e[0], l = e[1], c = e[2], 0 === l) o = r = n = 255 * c; else {
            for (b = [0, 0, 0], t = [0, 0, 0], s = .5 > c ? c * (1 + l) : c + l - c * l, d = 2 * c - s, a /= 360, b[0] = a + 1 / 3, b[1] = a, b[2] = a - 1 / 3, f = u = 0; 2 >= u; f = ++u) b[f] < 0 && (b[f] += 1), b[f] > 1 && (b[f] -= 1), t[f] = 6 * b[f] < 1 ? d + 6 * (s - d) * b[f] : 2 * b[f] < 1 ? s : 3 * b[f] < 2 ? d + (s - d) * (2 / 3 - b[f]) * 6 : d;
            i = [pe(255 * t[0]), pe(255 * t[1]), pe(255 * t[2])], o = i[0], r = i[1], n = i[2]
        }
        return e.length > 3 ? [o, r, n, e[3]] : [o, r, n]
    }, ae = function (e, n, t) {
        var r, a, f, c, u;
        return void 0 !== e && e.length >= 3 && (c = e, e = c[0], n = c[1], t = c[2]), e /= 255, n /= 255, t /= 255, f = Math.min(e, n, t), Z = Math.max(e, n, t), a = (Z + f) / 2, Z === f ? (u = 0, r = Number.NaN) : u = .5 > a ? (Z - f) / (Z + f) : (Z - f) / (2 - Z - f), e === Z ? r = (n - t) / (Z - f) : n === Z ? r = 2 + (t - e) / (Z - f) : t === Z && (r = 4 + (e - n) / (Z - f)), r *= 60, 0 > r && (r += 360), [r, u, a]
    }, v.hsl = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["hsl"]), function () {
        })
    }, i.hsl = B, e.prototype.hsl = function () {
        return ae(this._rgb)
    }, q = function () {
        var e, n, t, r, a, f, c, u, o, i, l, d, s, b, h, p, g, m;
        if (e = xe(arguments), a = e[0], p = e[1], m = e[2], m *= 255, 0 === p) o = r = n = m; else switch (360 === a && (a = 0), a > 360 && (a -= 360), 0 > a && (a += 360), a /= 60, f = L(a), t = a - f, c = m * (1 - p), u = m * (1 - p * t), g = m * (1 - p * (1 - t)), f) {
            case 0:
                i = [m, g, c], o = i[0], r = i[1], n = i[2];
                break;
            case 1:
                l = [u, m, c], o = l[0], r = l[1], n = l[2];
                break;
            case 2:
                d = [c, m, g], o = d[0], r = d[1], n = d[2];
                break;
            case 3:
                s = [c, u, m], o = s[0], r = s[1], n = s[2];
                break;
            case 4:
                b = [g, c, m], o = b[0], r = b[1], n = b[2];
                break;
            case 5:
                h = [m, c, u], o = h[0], r = h[1], n = h[2]
        }
        return o = pe(o), r = pe(r), n = pe(n), [o, r, n, e.length > 3 ? e[3] : 1]
    }, fe = function () {
        var e, n, t, r, a, f, c, u, o;
        return c = xe(arguments), f = c[0], t = c[1], e = c[2], a = Math.min(f, t, e), Z = Math.max(f, t, e), n = Z - a, o = Z / 255, 0 === Z ? (r = Number.NaN, u = 0) : (u = n / Z, f === Z && (r = (t - e) / n), t === Z && (r = 2 + (e - f) / n), e === Z && (r = 4 + (f - t) / n), r *= 60, 0 > r && (r += 360)), [r, u, o]
    }, v.hsv = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["hsv"]), function () {
        })
    }, i.hsv = q, e.prototype.hsv = function () {
        return fe(this._rgb)
    }, Q = function (e) {
        var n, t, r;
        return "number" === we(e) && e >= 0 && 16777215 >= e ? (r = e >> 16, t = e >> 8 & 255, n = 255 & e, [r, t, n, 1]) : (console.warn("unknown num color: " + e), [0, 0, 0, 1])
    }, ie = function () {
        var e, n, t, r;
        return r = xe(arguments), t = r[0], n = r[1], e = r[2], (t << 16) + (n << 8) + e
    }, v.num = function (n) {
        return new e(n, "num")
    }, e.prototype.num = function (e) {
        return null == e && (e = "rgb"), ie(this._rgb, e)
    }, i.num = Q, u.push({
        p: 10, test: function (e) {
            return 1 === arguments.length && "number" === we(e) && e >= 0 && 16777215 >= e ? "num" : void 0
        }
    }), _ = function (e) {
        var n, t, r, a, f, c, u, o;
        if (e = e.toLowerCase(), null != v.colors && v.colors[e]) return A(v.colors[e]);
        if (f = e.match(/rgb\(\s*(\-?\d+),\s*(\-?\d+)\s*,\s*(\-?\d+)\s*\)/)) {
            for (u = f.slice(1, 4), a = c = 0; 2 >= c; a = ++c) u[a] = +u[a];
            u[3] = 1
        } else if (f = e.match(/rgba\(\s*(\-?\d+),\s*(\-?\d+)\s*,\s*(\-?\d+)\s*,\s*([01]|[01]?\.\d+)\)/)) for (u = f.slice(1, 5), a = o = 0; 3 >= o; a = ++o) u[a] = +u[a]; else if (f = e.match(/rgb\(\s*(\-?\d+(?:\.\d+)?)%,\s*(\-?\d+(?:\.\d+)?)%\s*,\s*(\-?\d+(?:\.\d+)?)%\s*\)/)) {
            for (u = f.slice(1, 4), a = n = 0; 2 >= n; a = ++n) u[a] = pe(2.55 * u[a]);
            u[3] = 1
        } else if (f = e.match(/rgba\(\s*(\-?\d+(?:\.\d+)?)%,\s*(\-?\d+(?:\.\d+)?)%\s*,\s*(\-?\d+(?:\.\d+)?)%\s*,\s*([01]|[01]?\.\d+)\)/)) {
            for (u = f.slice(1, 5), a = t = 0; 2 >= t; a = ++t) u[a] = pe(2.55 * u[a]);
            u[3] = +u[3]
        } else (f = e.match(/hsl\(\s*(\-?\d+(?:\.\d+)?),\s*(\-?\d+(?:\.\d+)?)%\s*,\s*(\-?\d+(?:\.\d+)?)%\s*\)/)) ? (r = f.slice(1, 4), r[1] *= .01, r[2] *= .01, u = B(r), u[3] = 1) : (f = e.match(/hsla\(\s*(\-?\d+(?:\.\d+)?),\s*(\-?\d+(?:\.\d+)?)%\s*,\s*(\-?\d+(?:\.\d+)?)%\s*,\s*([01]|[01]?\.\d+)\)/)) && (r = f.slice(1, 4), r[1] *= .01, r[2] *= .01, u = B(r), u[3] = +f[4]);
        return u
    }, ne = function (e) {
        var n;
        return n = e[3] < 1 ? "rgba" : "rgb", "rgb" === n ? n + "(" + e.slice(0, 3).map(pe).join(",") + ")" : "rgba" === n ? n + "(" + e.slice(0, 3).map(pe).join(",") + "," + e[3] + ")" : void 0
    }, be = function (e) {
        return pe(100 * e) / 100
    }, j = function (e, n) {
        var t;
        return t = 1 > n ? "hsla" : "hsl", e[0] = be(e[0] || 0), e[1] = be(100 * e[1]) + "%", e[2] = be(100 * e[2]) + "%", "hsla" === t && (e[3] = n), t + "(" + e.join(",") + ")"
    }, i.css = function (e) {
        return _(e)
    }, v.css = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["css"]), function () {
        })
    }, e.prototype.css = function (e) {
        return null == e && (e = "rgb"), "rgb" === e.slice(0, 3) ? ne(this._rgb) : "hsl" === e.slice(0, 3) ? j(this.hsl(), this.alpha()) : void 0
    }, i.named = function (e) {
        return A(Me[e])
    }, u.push({
        p: 20, test: function (e) {
            return 1 === arguments.length && null != Me[e] ? "named" : void 0
        }
    }), e.prototype.name = function (e) {
        var n, t;
        arguments.length && (Me[e] && (this._rgb = A(Me[e])), this._rgb[3] = 1), n = this.hex();
        for (t in Me) if (n === Me[t]) return t;
        return n
    }, z = function () {
        var e, t, r, a;
        return a = xe(arguments), r = a[0], e = a[1], t = a[2], t *= n, [r, M(t) * e, me(t) * e]
    }, F = function () {
        var e, n, t, r, a, f, c, u, o, i, l;
        return t = xe(arguments), u = t[0], a = t[1], c = t[2], i = z(u, a, c), e = i[0], n = i[1], r = i[2], l = X(e, n, r), o = l[0], f = l[1], r = l[2], [$(o, 0, 255), $(f, 0, 255), $(r, 0, 255), t.length > 3 ? t[3] : 1]
    }, I = function () {
        var e, n, t, r, a, c;
        return c = xe(arguments), a = c[0], e = c[1], n = c[2], t = ve(e * e + n * n), r = (s(n, e) * f + 360) % 360, 0 === pe(1e4 * t) && (r = Number.NaN), [a, t, r]
    }, ue = function () {
        var e, n, t, r, a, f, c;
        return f = xe(arguments), a = f[0], t = f[1], n = f[2], c = ce(a, t, n), r = c[0], e = c[1], n = c[2], I(r, e, n)
    }, v.lch = function () {
        var n;
        return n = xe(arguments), new e(n, "lch")
    }, v.hcl = function () {
        var n;
        return n = xe(arguments), new e(n, "hcl")
    }, i.lch = F, i.hcl = function () {
        var e, n, t, r;
        return r = xe(arguments), n = r[0], e = r[1], t = r[2], F([t, e, n])
    }, e.prototype.lch = function () {
        return ue(this._rgb)
    }, e.prototype.hcl = function () {
        return ue(this._rgb).reverse()
    }, ee = function (e) {
        var n, t, r, a, f, c, u, o, i;
        return null == e && (e = "rgb"), o = xe(arguments), u = o[0], a = o[1], n = o[2], u /= 255, a /= 255, n /= 255, f = 1 - Math.max(u, Math.max(a, n)), r = 1 > f ? 1 / (1 - f) : 0, t = (1 - u - f) * r, c = (1 - a - f) * r, i = (1 - n - f) * r, [t, c, i, f]
    }, w = function () {
        var e, n, t, r, a, f, c, u, o;
        return n = xe(arguments), r = n[0], c = n[1], o = n[2], f = n[3], e = n.length > 4 ? n[4] : 1, 1 === f ? [0, 0, 0, e] : (u = r >= 1 ? 0 : pe(255 * (1 - r) * (1 - f)), a = c >= 1 ? 0 : pe(255 * (1 - c) * (1 - f)), t = o >= 1 ? 0 : pe(255 * (1 - o) * (1 - f)), [u, a, t, e])
    }, i.cmyk = function () {
        return w(xe(arguments))
    }, v.cmyk = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["cmyk"]), function () {
        })
    }, e.prototype.cmyk = function () {
        return ee(this._rgb)
    }, i.gl = function () {
        var e, n, t, r, a;
        for (r = function () {
            var e, t;
            e = xe(arguments), t = [];
            for (n in e) a = e[n], t.push(a);
            return t
        }.apply(this, arguments), e = t = 0; 2 >= t; e = ++t) r[e] *= 255;
        return r
    }, v.gl = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["gl"]), function () {
        })
    },e.prototype.gl = function () {
        var e;
        return e = this._rgb, [e[0] / 255, e[1] / 255, e[2] / 255, e[3]]
    },oe = function (e, n, t) {
        var r;
        return r = xe(arguments), e = r[0], n = r[1], t = r[2], e = U(e), n = U(n), t = U(t), .2126 * e + .7152 * n + .0722 * t
    },U = function (e) {
        return e /= 255, .03928 >= e ? e / 12.92 : J((e + .055) / 1.055, 2.4)
    },l = [],E = function (e, n, t, r) {
        var a, f, c, u;
        for (null == t && (t = .5), null == r && (r = "rgb"), "object" !== we(e) && (e = v(e)), "object" !== we(n) && (n = v(n)), c = 0, f = l.length; f > c; c++) if (a = l[c], r === a[0]) {
            u = a[1](e, n, t, r);
            break
        }
        if (null == u) throw"color mode " + r + " is not supported";
        return u.alpha(e.alpha() + t * (n.alpha() - e.alpha())), u
    },v.interpolate = E,e.prototype.interpolate = function (e, n, t) {
        return E(this, e, n, t)
    },v.mix = E,e.prototype.mix = e.prototype.interpolate,Y = function (n, t, r, a) {
        var f, c;
        return f = n._rgb, c = t._rgb, new e(f[0] + r * (c[0] - f[0]), f[1] + r * (c[1] - f[1]), f[2] + r * (c[2] - f[2]), a)
    },l.push(["rgb", Y]),e.prototype.luminance = function (e, n) {
        var t, r, a, f;
        return null == n && (n = "rgb"), arguments.length ? (0 === e ? this._rgb = [0, 0, 0, this._rgb[3]] : 1 === e ? this._rgb = [255, 255, 255, this._rgb[3]] : (r = 1e-7, a = 20, f = function (t, c) {
            var u, o;
            return o = t.interpolate(c, .5, n), u = o.luminance(), Math.abs(e - u) < r || !a-- ? o : u > e ? f(t, o) : f(o, c)
        }, t = oe(this._rgb), this._rgb = (t > e ? f(v("black"), this) : f(this, v("white"))).rgba()), this) : oe(this._rgb)
    },ye = function (e) {
        var n, t, r, a;
        return a = e / 100, 66 > a ? (r = 255, t = -155.25485562709179 - .44596950469579133 * (t = a - 2) + 104.49216199393888 * D(t), n = 20 > a ? 0 : -254.76935184120902 + .8274096064007395 * (n = a - 10) + 115.67994401066147 * D(n)) : (r = 351.97690566805693 + .114206453784165 * (r = a - 55) - 40.25366309332127 * D(r), t = 325.4494125711974 + .07943456536662342 * (t = a - 50) - 28.0852963507957 * D(t), n = 255), y([r, t, n])
    },le = function () {
        var e, n, t, r, a, f, c, u, o;
        for (c = xe(arguments), f = c[0], t = c[1], e = c[2], a = 1e3, r = 4e4, n = .4; r - a > n;) o = .5 * (r + a), u = ye(o), u[2] / u[0] >= e / f ? r = o : a = o;
        return pe(o)
    },v.temperature = v.kelvin = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["temperature"]), function () {
        })
    },i.temperature = i.kelvin = i.K = ye,e.prototype.temperature = function () {
        return le(this._rgb)
    },e.prototype.kelvin = e.prototype.temperature,v.contrast = function (n, t) {
        var r, a, f, c;
        return ("string" === (f = we(n)) || "number" === f) && (n = new e(n)), ("string" === (c = we(t)) || "number" === c) && (t = new e(t)), r = n.luminance(), a = t.luminance(), r > a ? (r + .05) / (a + .05) : (a + .05) / (r + .05)
    },e.prototype.get = function (e) {
        var n, t, r, a, f, c;
        return r = this, f = e.split("."), a = f[0], n = f[1], c = r[a](), n ? (t = a.indexOf(n), t > -1 ? c[t] : console.warn("unknown channel " + n + " in mode " + a)) : c
    },e.prototype.set = function (e, n) {
        var t, r, a, f, c, u;
        if (a = this, c = e.split("."), f = c[0], t = c[1], t) if (u = a[f](), r = f.indexOf(t), r > -1) if ("string" === we(n)) switch (n.charAt(0)) {
            case"+":
                u[r] += +n;
                break;
            case"-":
                u[r] += +n;
                break;
            case"*":
                u[r] *= +n.substr(1);
                break;
            case"/":
                u[r] /= +n.substr(1);
                break;
            default:
                u[r] = +n
        } else u[r] = n; else console.warn("unknown channel " + t + " in mode " + f); else u = n;
        return a._rgb = v(u, f).alpha(a.alpha())._rgb, a
    },e.prototype.darken = function (e) {
        var n, r;
        return null == e && (e = 1), r = this, n = r.lab(), n[0] -= t.Kn * e, v.lab(n).alpha(r.alpha())
    },e.prototype.brighten = function (e) {
        return null == e && (e = 1), this.darken(-e)
    },e.prototype.darker = e.prototype.darken,e.prototype.brighter = e.prototype.brighten,e.prototype.saturate = function (e) {
        var n, r;
        return null == e && (e = 1), r = this, n = r.lch(), n[1] += e * t.Kn, n[1] < 0 && (n[1] = 0), v.lch(n).alpha(r.alpha())
    },e.prototype.desaturate = function (e) {
        return null == e && (e = 1), this.saturate(-e)
    },e.prototype.premultiply = function () {
        var e, n;
        return n = this.rgb(), e = this.alpha(), v(n[0] * e, n[1] * e, n[2] * e, e)
    },h = function (e, n, t) {
        if (!h[t]) throw"unknown blend mode " + t;
        return h[t](e, n)
    },p = function (e) {
        return function (n, t) {
            var r, a;
            return r = v(t).rgb(), a = v(n).rgb(), v(e(r, a), "rgb")
        }
    },N = function (e) {
        return function (n, t) {
            var r, a, f;
            for (f = [], r = a = 0; 3 >= a; r = ++a) f[r] = e(n[r], t[r]);
            return f
        }
    },K = function (e) {
        return e
    },H = function (e, n) {
        return e * n / 255
    },k = function (e, n) {
        return e > n ? n : e
    },G = function (e, n) {
        return e > n ? e : n
    },ge = function (e, n) {
        return 255 * (1 - (1 - e / 255) * (1 - n / 255))
    },W = function (e, n) {
        return 128 > n ? 2 * e * n / 255 : 255 * (1 - 2 * (1 - e / 255) * (1 - n / 255))
    },m = function (e, n) {
        return 255 * (1 - (1 - n / 255) / (e / 255))
    },O = function (e, n) {
        return 255 === e ? 255 : (e = 255 * (n / 255) / (1 - e / 255), e > 255 ? 255 : e)
    },h.normal = p(N(K)),h.multiply = p(N(H)),h.screen = p(N(ge)),h.overlay = p(N(W)),h.darken = p(N(k)),h.lighten = p(N(G)),h.dodge = p(N(O)),h.burn = p(N(m)),v.blend = h,v.analyze = function (e) {
        var n, t, r, a;
        for (r = {
            min: Number.MAX_VALUE,
            max: -1 * Number.MAX_VALUE,
            sum: 0,
            values: [],
            count: 0
        }, t = 0, n = e.length; n > t; t++) a = e[t], null == a || isNaN(a) || (r.values.push(a), r.sum += a, a < r.min && (r.min = a), a > r.max && (r.max = a), r.count += 1);
        return r.domain = [r.min, r.max], r.limits = function (e, n) {
            return v.limits(r, e, n)
        }, r
    },v.scale = function (e) {
        var n, t, r, a, f, c, u, o, i, l, d, s, b, h, p, g, m, y, w, x, M;
        return i = "rgb", l = v("#ccc"), h = 0, c = !1, f = [0, 1], b = [], s = [0, 0], n = !1, r = [], d = !1, o = 0, u = 1, a = !1, t = {}, x = function (e) {
            var n, t, a, f, c, u, o;
            if (null == e && (e = ["#fff", "#000"]), null != e && "string" === we(e) && null != (null != (f = v.brewer) ? f[e] : void 0) && (e = v.brewer[e]), "array" === we(e)) {
                for (e = e.slice(0), n = a = 0, c = e.length - 1; c >= 0 ? c >= a : a >= c; n = c >= 0 ? ++a : --a) t = e[n], "string" === we(t) && (e[n] = v(t));
                for (b.length = 0, n = o = 0, u = e.length - 1; u >= 0 ? u >= o : o >= u; n = u >= 0 ? ++o : --o) b.push(n / (e.length - 1))
            }
            return w(), r = e
        }, m = function (e) {
            var t, r;
            if (null != n) {
                for (r = n.length - 1, t = 0; r > t && e >= n[t];) t++;
                return t - 1
            }
            return 0
        }, M = function (e) {
            return e
        }, p = function (e) {
            var t, r, a, f, c;
            return c = e, n.length > 2 && (f = n.length - 1, t = m(e), a = n[0] + (n[1] - n[0]) * (0 + .5 * h), r = n[f - 1] + (n[f] - n[f - 1]) * (1 - .5 * h), c = o + (n[t] + .5 * (n[t + 1] - n[t]) - a) / (r - a) * (u - o)), c
        }, y = function (e, a) {
            var f, c, d, h, p, g, y, w;
            if (null == a && (a = !1), isNaN(e)) return l;
            if (a ? w = e : n && n.length > 2 ? (f = m(e), w = f / (n.length - 2), w = s[0] + w * (1 - s[0] - s[1])) : u !== o ? (w = (e - o) / (u - o), w = s[0] + w * (1 - s[0] - s[1]), w = Math.min(1, Math.max(0, w))) : w = 1, a || (w = M(w)), h = Math.floor(1e4 * w), t[h]) c = t[h]; else {
                if ("array" === we(r)) for (d = p = 0, y = b.length - 1; y >= 0 ? y >= p : p >= y; d = y >= 0 ? ++p : --p) {
                    if (g = b[d], g >= w) {
                        c = r[d];
                        break
                    }
                    if (w >= g && d === b.length - 1) {
                        c = r[d];
                        break
                    }
                    if (w > g && w < b[d + 1]) {
                        w = (w - g) / (b[d + 1] - g), c = v.interpolate(r[d], r[d + 1], w, i);
                        break
                    }
                } else "function" === we(r) && (c = r(w));
                t[h] = c
            }
            return c
        }, w = function () {
            return t = {}
        }, x(e), g = function (e) {
            var n;
            return n = v(y(e)), d && n[d] ? n[d]() : n
        }, g.classes = function (e) {
            var t;
            return null != e ? ("array" === we(e) ? (n = e, f = [e[0], e[e.length - 1]]) : (t = v.analyze(f), n = 0 === e ? [t.min, t.max] : v.limits(t, "e", e)), g) : n
        }, g.domain = function (e) {
            var n, t, a, c, i, l, d;
            if (!arguments.length) return f;
            if (o = e[0], u = e[e.length - 1], b = [], a = r.length, e.length === a && o !== u) for (i = 0, c = e.length; c > i; i++) t = e[i], b.push((t - o) / (u - o)); else for (n = d = 0, l = a - 1; l >= 0 ? l >= d : d >= l; n = l >= 0 ? ++d : --d) b.push(n / (a - 1));
            return f = [o, u], g
        }, g.mode = function (e) {
            return arguments.length ? (i = e, w(), g) : i
        }, g.range = function (e, n) {
            return x(e, n), g
        }, g.out = function (e) {
            return d = e, g
        }, g.spread = function (e) {
            return arguments.length ? (h = e, g) : h
        }, g.correctLightness = function (e) {
            return null == e && (e = !0), a = e, w(), M = a ? function (e) {
                var n, t, r, a, f, c, u, o, i;
                for (n = y(0, !0).lab()[0], t = y(1, !0).lab()[0], u = n > t, r = y(e, !0).lab()[0], f = n + (t - n) * e, a = r - f, o = 0, i = 1, c = 20; Math.abs(a) > .01 && c-- > 0;) !function () {
                    return u && (a *= -1), 0 > a ? (o = e, e += .5 * (i - e)) : (i = e, e += .5 * (o - e)), r = y(e, !0).lab()[0], a = r - f
                }();
                return e
            } : function (e) {
                return e
            }, g
        }, g.padding = function (e) {
            return null != e ? ("number" === we(e) && (e = [e, e]), s = e, g) : s
        }, g.colors = function () {
            var t, r, a, c, u, o, i, l, d;
            if (c = 0, u = "hex", 1 === arguments.length && ("string" === we(arguments[0]) ? u = arguments[0] : c = arguments[0]), 2 === arguments.length && (c = arguments[0], u = arguments[1]), c) return r = f[0], t = f[1] - r, function () {
                i = [];
                for (var e = 0; c >= 0 ? c > e : e > c; c >= 0 ? e++ : e--) i.push(e);
                return i
            }.apply(this).map(function (e) {
                return g(r + e / (c - 1) * t)[u]()
            });
            if (e = [], l = [], n && n.length > 2) for (a = d = 1, o = n.length; o >= 1 ? o > d : d > o; a = o >= 1 ? ++d : --d) l.push(.5 * (n[a - 1] + n[a])); else l = f;
            return l.map(function (e) {
                return g(e)[u]()
            })
        }, g
    },null == v.scales && (v.scales = {}),v.scales.cool = function () {
        return v.scale([v.hsl(180, 1, .9), v.hsl(250, .7, .4)])
    },v.scales.hot = function () {
        return v.scale(["#000", "#f00", "#ff0", "#fff"], [0, .25, .75, 1]).mode("rgb")
    },v.analyze = function (e, n, t) {
        var r, a, f, c, u, o, i;
        if (u = {
            min: Number.MAX_VALUE,
            max: -1 * Number.MAX_VALUE,
            sum: 0,
            values: [],
            count: 0
        }, null == t && (t = function () {
            return !0
        }), r = function (e) {
            null == e || isNaN(e) || (u.values.push(e), u.sum += e, e < u.min && (u.min = e), e > u.max && (u.max = e), u.count += 1)
        }, i = function (e, a) {
            return t(e, a) ? r(null != n && "function" === we(n) ? n(e) : null != n && "string" === we(n) || "number" === we(n) ? e[n] : e) : void 0
        }, "array" === we(e)) for (c = 0, f = e.length; f > c; c++) o = e[c], i(o); else for (a in e) o = e[a], i(o, a);
        return u.domain = [u.min, u.max], u.limits = function (e, n) {
            return v.limits(u, e, n)
        }, u
    },v.limits = function (e, n, t) {
        var r, a, f, c, u, o, i, l, s, b, h, p, g, m, y, w, x, M, _, k, O, N, A, P, j, B, q, E, R, S, C, Y, I, X, T, z,
            F, G, $, U, V, H, K, Q, W, ee, ne, te, re, ae, fe, ce, ue, oe, ie;
        if (null == n && (n = "equal"), null == t && (t = 7), "array" === we(e) && (e = v.analyze(e)), j = e.min, Z = e.max, fe = e.sum, oe = e.values.sort(function (e, n) {
            return e - n
        }), A = [], "c" === n.substr(0, 1) && (A.push(j), A.push(Z)), "e" === n.substr(0, 1)) {
            for (A.push(j), k = C = 1, T = t - 1; T >= 1 ? T >= C : C >= T; k = T >= 1 ? ++C : --C) A.push(j + k / t * (Z - j));
            A.push(Z)
        } else if ("l" === n.substr(0, 1)) {
            if (0 >= j) throw"Logarithmic scales are only possible for values > 0";
            for (B = Math.LOG10E * D(j), P = Math.LOG10E * D(Z), A.push(j), k = ie = 1, z = t - 1; z >= 1 ? z >= ie : ie >= z; k = z >= 1 ? ++ie : --ie) A.push(J(10, B + k / t * (P - B)));
            A.push(Z)
        } else if ("q" === n.substr(0, 1)) {
            for (A.push(j), k = r = 1, H = t - 1; H >= 1 ? H >= r : r >= H; k = H >= 1 ? ++r : --r) Y = oe.length * k / t, I = L(Y), I === Y ? A.push(oe[I]) : (X = Y - I, A.push(oe[I] * X + oe[I + 1] * (1 - X)));
            A.push(Z)
        } else if ("k" === n.substr(0, 1)) {
            for (E = oe.length, m = new Array(E), M = new Array(t), ae = !0, R = 0, w = null, w = [], w.push(j), k = a = 1, K = t - 1; K >= 1 ? K >= a : a >= K; k = K >= 1 ? ++a : --a) w.push(j + k / t * (Z - j));
            for (w.push(Z); ae;) {
                for (O = f = 0, Q = t - 1; Q >= 0 ? Q >= f : f >= Q; O = Q >= 0 ? ++f : --f) M[O] = 0;
                for (k = c = 0, W = E - 1; W >= 0 ? W >= c : c >= W; k = W >= 0 ? ++c : --c) {
                    for (ue = oe[k], q = Number.MAX_VALUE, O = u = 0, ee = t - 1; ee >= 0 ? ee >= u : u >= ee; O = ee >= 0 ? ++u : --u) _ = d(w[O] - ue), q > _ && (q = _, y = O);
                    M[y]++, m[k] = y
                }
                for (S = new Array(t), O = o = 0, ne = t - 1; ne >= 0 ? ne >= o : o >= ne; O = ne >= 0 ? ++o : --o) S[O] = null;
                for (k = i = 0, te = E - 1; te >= 0 ? te >= i : i >= te; k = te >= 0 ? ++i : --i) x = m[k], null === S[x] ? S[x] = oe[k] : S[x] += oe[k];
                for (O = l = 0, re = t - 1; re >= 0 ? re >= l : l >= re; O = re >= 0 ? ++l : --l) S[O] *= 1 / M[O];
                for (ae = !1, O = s = 0, F = t - 1; F >= 0 ? F >= s : s >= F; O = F >= 0 ? ++s : --s) if (S[O] !== w[k]) {
                    ae = !0;
                    break
                }
                w = S, R++, R > 200 && (ae = !1)
            }
            for (N = {}, O = b = 0, G = t - 1; G >= 0 ? G >= b : b >= G; O = G >= 0 ? ++b : --b) N[O] = [];
            for (k = h = 0, $ = E - 1; $ >= 0 ? $ >= h : h >= $; k = $ >= 0 ? ++h : --h) x = m[k], N[x].push(oe[k]);
            for (ce = [], O = p = 0, U = t - 1; U >= 0 ? U >= p : p >= U; O = U >= 0 ? ++p : --p) ce.push(N[O][0]), ce.push(N[O][N[O].length - 1]);
            for (ce = ce.sort(function (e, n) {
                return e - n
            }), A.push(ce[0]), k = g = 1, V = ce.length - 1; V >= g; k = g += 2) isNaN(ce[k]) || A.push(ce[k])
        }
        return A
    },P = function (e, n, t) {
        var r, f, u, o;
        return r = xe(arguments), e = r[0], n = r[1], t = r[2], e /= 360, 1 / 3 > e ? (f = (1 - n) / 3, o = (1 + n * M(c * e) / M(a - c * e)) / 3, u = 1 - (f + o)) : 2 / 3 > e ? (e -= 1 / 3, o = (1 - n) / 3, u = (1 + n * M(c * e) / M(a - c * e)) / 3, f = 1 - (o + u)) : (e -= 2 / 3, u = (1 - n) / 3, f = (1 + n * M(c * e) / M(a - c * e)) / 3, o = 1 - (u + f)), o = $(t * o * 3), u = $(t * u * 3), f = $(t * f * 3), [255 * o, 255 * u, 255 * f, r.length > 3 ? r[3] : 1]
    },re = function () {
        var e, n, t, r, a, f, u, o;
        return u = xe(arguments), f = u[0], n = u[1], e = u[2], c = 2 * Math.PI, f /= 255, n /= 255, e /= 255, a = Math.min(f, n, e), r = (f + n + e) / 3, o = 1 - a / r, 0 === o ? t = 0 : (t = (f - n + (f - e)) / 2, t /= Math.sqrt((f - n) * (f - n) + (f - e) * (n - e)), t = Math.acos(t), e > n && (t = c - t), t /= c), [360 * t, o, r]
    },v.hsi = function () {
        return function (e, n, t) {
            t.prototype = e.prototype;
            var r = new t, a = e.apply(r, n);
            return Object(a) === a ? a : r
        }(e, Oe.call(arguments).concat(["hsi"]), function () {
        })
    },i.hsi = P,e.prototype.hsi = function () {
        return re(this._rgb)
    },R = function (e, n, t, r) {
        var a, f, c, u, o, i, l, d, s, b, h, p, g;
        return "hsl" === r ? (p = e.hsl(), g = n.hsl()) : "hsv" === r ? (p = e.hsv(), g = n.hsv()) : "hsi" === r ? (p = e.hsi(), g = n.hsi()) : ("lch" === r || "hcl" === r) && (r = "hcl", p = e.hcl(), g = n.hcl()), "h" === r.substr(0, 1) && (c = p[0], b = p[1], i = p[2], u = g[0], h = g[1], l = g[2]), isNaN(c) || isNaN(u) ? isNaN(c) ? isNaN(u) ? f = Number.NaN : (f = u, 1 !== i && 0 !== i || "hsv" === r || (s = h)) : (f = c, 1 !== l && 0 !== l || "hsv" === r || (s = b)) : (a = u > c && u - c > 180 ? u - (c + 360) : c > u && c - u > 180 ? u + 360 - c : u - c, f = c + t * a), null == s && (s = b + t * (h - b)), o = i + t * (l - i), d = v[r](f, s, o)
    },l = l.concat(function () {
        var e, n, t, r;
        for (t = ["hsv", "hsl", "hsi", "hcl", "lch"], r = [], n = 0, e = t.length; e > n; n++) V = t[n], r.push([V, R]);
        return r
    }()),C = function (e, n, t) {
        var r, a;
        return r = e.num(), a = n.num(), v.num(r + (a - r) * t, "num")
    },l.push(["num", C]),S = function (n, t, r, a) {
        var f, c, u;
        return c = n.lab(), u = t.lab(), f = new e(c[0] + r * (u[0] - c[0]), c[1] + r * (u[1] - c[1]), c[2] + r * (u[2] - c[2]), a)
    },l.push(["lab", S])
}.call(this), function () {
    function e(e) {
        this._value = e
    }

    function n(e, n, t, r) {
        var a, f, c = Math.pow(10, n);
        return f = (t(e * c) / c).toFixed(n), r && (a = new RegExp("0{1," + r + "}$"), f = f.replace(a, "")), f
    }

    function t(e, n, t) {
        var r;
        return r = n.indexOf("$") > -1 ? a(e, n, t) : n.indexOf("%") > -1 ? f(e, n, t) : n.indexOf(":") > -1 ? c(e, n) : o(e._value, n, t)
    }

    function r(e, n) {
        var t, r, a, f, c, o = n, i = ["KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"], l = !1;
        if (n.indexOf(":") > -1) e._value = u(n); else if (n === g) e._value = 0; else {
            for ("." !== h[p].delimiters.decimal && (n = n.replace(/\./g, "").replace(h[p].delimiters.decimal, ".")), t = new RegExp("[^a-zA-Z]" + h[p].abbreviations.thousand + "(?:\\)|(\\" + h[p].currency.symbol + ")?(?:\\))?)?$"), r = new RegExp("[^a-zA-Z]" + h[p].abbreviations.million + "(?:\\)|(\\" + h[p].currency.symbol + ")?(?:\\))?)?$"), a = new RegExp("[^a-zA-Z]" + h[p].abbreviations.billion + "(?:\\)|(\\" + h[p].currency.symbol + ")?(?:\\))?)?$"), f = new RegExp("[^a-zA-Z]" + h[p].abbreviations.trillion + "(?:\\)|(\\" + h[p].currency.symbol + ")?(?:\\))?)?$"), c = 0; c <= i.length && !(l = n.indexOf(i[c]) > -1 ? Math.pow(1024, c + 1) : !1); c++) ;
            e._value = (l ? l : 1) * (o.match(t) ? Math.pow(10, 3) : 1) * (o.match(r) ? Math.pow(10, 6) : 1) * (o.match(a) ? Math.pow(10, 9) : 1) * (o.match(f) ? Math.pow(10, 12) : 1) * (n.indexOf("%") > -1 ? .01 : 1) * ((n.split("-").length + Math.min(n.split("(").length - 1, n.split(")").length - 1)) % 2 ? 1 : -1) * Number(n.replace(/[^0-9\.]+/g, "")), e._value = l ? Math.ceil(e._value) : e._value
        }
        return e._value
    }

    function a(e, n, t) {
        var r, a, f = n.indexOf("$"), c = n.indexOf("("), u = n.indexOf("-"), i = "";
        return n.indexOf(" $") > -1 ? (i = " ", n = n.replace(" $", "")) : n.indexOf("$ ") > -1 ? (i = " ", n = n.replace("$ ", "")) : n = n.replace("$", ""), a = o(e._value, n, t), 1 >= f ? a.indexOf("(") > -1 || a.indexOf("-") > -1 ? (a = a.split(""), r = 1, (c > f || u > f) && (r = 0), a.splice(r, 0, h[p].currency.symbol + i), a = a.join("")) : a = h[p].currency.symbol + i + a : a.indexOf(")") > -1 ? (a = a.split(""), a.splice(-1, 0, i + h[p].currency.symbol), a = a.join("")) : a = a + i + h[p].currency.symbol, a
    }

    function f(e, n, t) {
        var r, a = "", f = 100 * e._value;
        return n.indexOf(" %") > -1 ? (a = " ", n = n.replace(" %", "")) : n = n.replace("%", ""), r = o(f, n, t), r.indexOf(")") > -1 ? (r = r.split(""), r.splice(-1, 0, a + "%"), r = r.join("")) : r = r + a + "%", r
    }

    function c(e) {
        var n = Math.floor(e._value / 60 / 60), t = Math.floor((e._value - 60 * n * 60) / 60),
            r = Math.round(e._value - 60 * n * 60 - 60 * t);
        return n + ":" + (10 > t ? "0" + t : t) + ":" + (10 > r ? "0" + r : r)
    }

    function u(e) {
        var n = e.split(":"), t = 0;
        return 3 === n.length ? (t += 60 * Number(n[0]) * 60, t += 60 * Number(n[1]), t += Number(n[2])) : 2 === n.length && (t += 60 * Number(n[0]), t += Number(n[1])), Number(t)
    }

    function o(e, t, r) {
        var a, f, c, u, o, i, l = !1, d = !1, s = !1, b = "", m = !1, v = !1, y = !1, w = !1, x = !1, M = "", _ = "",
            k = Math.abs(e), O = ["B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB"], N = "", L = !1;
        if (0 === e && null !== g) return g;
        if (t.indexOf("(") > -1 ? (l = !0, t = t.slice(1, -1)) : t.indexOf("+") > -1 && (d = !0, t = t.replace(/\+/g, "")), t.indexOf("a") > -1 && (m = t.indexOf("aK") >= 0, v = t.indexOf("aM") >= 0, y = t.indexOf("aB") >= 0, w = t.indexOf("aT") >= 0, x = m || v || y || w, t.indexOf(" a") > -1 ? (b = " ", t = t.replace(" a", "")) : t = t.replace("a", ""), k >= Math.pow(10, 12) && !x || w ? (b += h[p].abbreviations.trillion, e /= Math.pow(10, 12)) : k < Math.pow(10, 12) && k >= Math.pow(10, 9) && !x || y ? (b += h[p].abbreviations.billion, e /= Math.pow(10, 9)) : k < Math.pow(10, 9) && k >= Math.pow(10, 6) && !x || v ? (b += h[p].abbreviations.million, e /= Math.pow(10, 6)) : (k < Math.pow(10, 6) && k >= Math.pow(10, 3) && !x || m) && (b += h[p].abbreviations.thousand, e /= Math.pow(10, 3))), t.indexOf("b") > -1) for (t.indexOf(" b") > -1 ? (M = " ", t = t.replace(" b", "")) : t = t.replace("b", ""), c = 0; c <= O.length; c++) if (a = Math.pow(1024, c), f = Math.pow(1024, c + 1), e >= a && f > e) {
            M += O[c], a > 0 && (e /= a);
            break
        }
        return t.indexOf("o") > -1 && (t.indexOf(" o") > -1 ? (_ = " ", t = t.replace(" o", "")) : t = t.replace("o", ""), _ += h[p].ordinal(e)), t.indexOf("[.]") > -1 && (s = !0, t = t.replace("[.]", ".")), u = e.toString().split(".")[0], o = t.split(".")[1], i = t.indexOf(","), o ? (o.indexOf("[") > -1 ? (o = o.replace("]", ""), o = o.split("["), N = n(e, o[0].length + o[1].length, r, o[1].length)) : N = n(e, o.length, r), u = N.split(".")[0], N = N.split(".")[1].length ? h[p].delimiters.decimal + N.split(".")[1] : "", s && 0 === Number(N.slice(1)) && (N = "")) : u = n(e, null, r), u.indexOf("-") > -1 && (u = u.slice(1), L = !0), i > -1 && (u = u.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1" + h[p].delimiters.thousands)), 0 === t.indexOf(".") && (u = ""), (l && L ? "(" : "") + (!l && L ? "-" : "") + (!L && d ? "+" : "") + u + N + (_ ? _ : "") + (b ? b : "") + (M ? M : "") + (l && L ? ")" : "")
    }

    function i(e, n) {
        h[e] = n
    }

    function l(e) {
        var n = e.toString().split(".");
        return n.length < 2 ? 1 : Math.pow(10, n[1].length)
    }

    function d() {
        var e = Array.prototype.slice.call(arguments);
        return e.reduce(function (e, n) {
            var t = l(e), r = l(n);
            return t > r ? t : r
        }, -1 / 0)
    }

    var s, b = "1.5.3", h = {}, p = "en", g = null, m = "0,0", v = "undefined" != typeof module && module.exports;
    s = function (n) {
        return s.isNumeral(n) ? n = n.value() : 0 === n || "undefined" == typeof n ? n = 0 : Number(n) || (n = s.fn.unformat(n)), new e(Number(n))
    }, s.version = b, s.isNumeral = function (n) {
        return n instanceof e
    }, s.language = function (e, n) {
        if (!e) return p;
        if (e && !n) {
            if (!h[e]) throw new Error("Unknown language : " + e);
            p = e
        }
        return (n || !h[e]) && i(e, n), s
    }, s.languageData = function (e) {
        if (!e) return h[p];
        if (!h[e]) throw new Error("Unknown language : " + e);
        return h[e]
    }, s.language("en", {
        delimiters: {thousands: ",", decimal: "."},
        abbreviations: {thousand: "k", million: "m", billion: "b", trillion: "t"},
        ordinal: function (e) {
            var n = e % 10;
            return 1 === ~~(e % 100 / 10) ? "th" : 1 === n ? "st" : 2 === n ? "nd" : 3 === n ? "rd" : "th"
        },
        currency: {symbol: "$"}
    }), s.zeroFormat = function (e) {
        g = "string" == typeof e ? e : null
    }, s.defaultFormat = function (e) {
        m = "string" == typeof e ? e : "0.0"
    }, "function" != typeof Array.prototype.reduce && (Array.prototype.reduce = function (e, n) {
        if (null === this || "undefined" == typeof this) throw new TypeError("Array.prototype.reduce called on null or undefined");
        if ("function" != typeof e) throw new TypeError(e + " is not a function");
        var t, r, a = this.length >>> 0, f = !1;
        for (1 < arguments.length && (r = n, f = !0), t = 0; a > t; ++t) this.hasOwnProperty(t) && (f ? r = e(r, this[t], t, this) : (r = this[t], f = !0));
        if (!f) throw new TypeError("Reduce of empty array with no initial value");
        return r
    }), s.fn = e.prototype = {
        clone: function () {
            return s(this)
        }, format: function (e, n) {
            return t(this, e ? e : m, void 0 !== n ? n : Math.round)
        }, unformat: function (e) {
            return "[object Number]" === Object.prototype.toString.call(e) ? e : r(this, e ? e : m)
        }, value: function () {
            return this._value
        }, valueOf: function () {
            return this._value
        }, set: function (e) {
            return this._value = Number(e), this
        }, add: function (e) {
            function n(e, n) {
                return e + t * n
            }

            var t = d.call(null, this._value, e);
            return this._value = [this._value, e].reduce(n, 0) / t, this
        }, subtract: function (e) {
            function n(e, n) {
                return e - t * n
            }

            var t = d.call(null, this._value, e);
            return this._value = [e].reduce(n, this._value * t) / t, this
        }, multiply: function (e) {
            function n(e, n) {
                var t = d(e, n);
                return e * t * n * t / (t * t)
            }

            return this._value = [this._value, e].reduce(n, 1), this
        }, divide: function (e) {
            function n(e, n) {
                var t = d(e, n);
                return e * t / (n * t)
            }

            return this._value = [this._value, e].reduce(n), this
        }, difference: function (e) {
            return Math.abs(s(this._value).subtract(e).value())
        }
    }, v && (module.exports = s),
    "undefined" == typeof ender && (this.numeral = s), "function" == typeof define && define.amd && define([], function () {
        return s
    })
}.call(this), L.BubbleLayer = (L.Layer ? L.Layer : L.Class).extend({
    initialize: function (e, n) {
        console.log("initalized: ", n, e), this._geojson = e, n.max_radius = n.hasOwnProperty("max_radius") ? n.max_radius : 35, n.legend = n.hasOwnProperty("legend") ? n.legend : !0, n.tooltip = n.hasOwnProperty("tooltip") ? n.tooltip : !0, n.scale = n.hasOwnProperty("scale") ? n.scale : !1, n.style = n.hasOwnProperty("style") ? n.style : {
            radius: 10,
            fillColor: "#74acb8",
            color: "#555",
            weight: 1,
            opacity: .5,
            fillOpacity: .5
        }, L.setOptions(this, n);
        var t = this._hasRequiredProp(this.options.property);
        if (!t) throw"Error: you must provide an amount property that is include in every GeoJSON feature"
    }, addTo: function (e) {
        return e.addLayer(this), this
    }, onAdd: function (e) {
        this._map = e;
        var n = this.createLayer();
        this._layer = n, e.addLayer(n), this.options.tooltip && this.showTooltip(n), this.options.legend && this.showLegend(this._scale, this._max)
    }, createLayer: function () {
        var e = this._getMax(this._geojson), n = 3 * Math.PI * 3,
            t = Math.PI * this.options.max_radius * this.options.max_radius,
            r = d3_scale.scaleLinear().domain([0, e]).range([n, t]),
            a = d3_scale.scaleLinear().domain([0, e]).range([0, 1]);
        this._scale = r, this._normal = a, this._max = e;
        var f = this.options.property, c = this.options.style, u = !1;
        return this.options.scale && (u = chroma.scale(this.options.scale)), new L.geoJson(this._geojson, {
            pointToLayer: function (e, n) {
                var t = e.properties[f], o = r(t), i = Math.sqrt(o / Math.PI);
                c.radius = i, u && (c.fillColor = u(a(t))), c.color = chroma(c.fillColor).darken().hex();
                var l = L.circleMarker(n, c);
                return l
            }
        })
    }, onRemove: function (e) {
        this._map = e, e.removeLayer(this._layer)
    }, showLegend: function (e, n) {
        var t = L.control({position: "bottomright"}), r = this.options.max_radius, a = this.options.style.fillColor,
            f = !1, c = this.options.style.opacity, u = d3_scale.scaleLinear().domain([0, n]).range([0, 1]);
        this.options.scale && (f = chroma.scale(this.options.scale)), t.onAdd = function () {
            var t = L.DomUtil.create("div", "info legend");
            t.innerHTML += "<strong>" + bubbles.options.property + "</strong><br/>", t.style = "background-color: #FFF; padding: 8px; font-size: 14px; text-transform: capitalize";
            for (var o = 3; o > 0; o--) {
                var i = e(n / o / 2), l = Math.sqrt(i / Math.PI), d = L.DomUtil.create("div", "bubble");
                f && (a = f(u(n / o))), d.innerHTML = '<svg height="' + 2 * r + '" width="' + (2 * r - r / 2) + '"><circle cx="' + (l + 1) + '" cy="' + r + '" r="' + l + '" stroke="' + chroma(a).darken().hex() + '" stroke-width="1" opacity="' + c + '" fill="' + a + '" /><text font-size="11" text-anchor="middle" x="' + l + '" y="' + 2 * r + '" fill="#AAA">' + numeral(n / o).format("0 a"), d.style = "float:left; width: " + l + ";", t.appendChild(d)
            }
            return t
        }, t.addTo(map)
    }, showTooltip: function (e) {
        e.on("mouseover", function (e) {
            var n = "", t = e.layer.feature.properties;
            for (var r in t) t.hasOwnProperty(r) && (n += "<strong>" + r + "</strong>: " + t[r] + "</br>");
            e.layer.bindPopup(n), e.layer.openPopup()
        }), e.on("mouseout", function (e) {
            e.layer.closePopup()
        })
    }, _getMax: function () {
        for (var e = 0, n = this._geojson.features, t = this.options.property, r = 0; r < n.length; r++) n[r].properties[t] > e && (e = n[r].properties[t]);
        return e
    }, _hasRequiredProp: function (e) {
        for (var n = !0, t = this._geojson.features, r = 0; r < t.length; r++) t[r].properties.hasOwnProperty(e) !== !0 && (n = !1);
        return n
    }
}), L.bubbleLayer = function (e, n) {
    return new L.BubbleLayer(e, n)
};