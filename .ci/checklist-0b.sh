#!/usr/bin/env bash
# Checklist de conversión por archivo — nivel 0b de la migración de AssetPlanner a MAN.
# Fuente: traz-tools doc/migracion/MIGRACION-ASSETPLANNER.md §3.4.
#
# Reglas, por archivo .php:
#   R1  cero userdata('user_data')        -> usar empresa() / userId() / userNick()
#   R2  cero $this->db                    -> MAN no accede a base (ADR-014)
#   R3  cero assetDB                      -> MAN no accede a base (ADR-014)
#   R4  cero URLs index.php/X             -> usar la constante MAN
#   R5  toda vista referenciada existe    -> load->view('x/y') => views/x/y.php
#
# Los archivos listados en .ci/portados.txt son BLOQUEANTES: un solo hallazgo hace fallar el CI.
# El resto se informa (cantidades) sin fallar, para seguir el avance sobre el fork.
#
# Uso (en la raíz del repo): bash .ci/checklist-0b.sh

set -uo pipefail

PORTADOS_FILE=".ci/portados.txt"
fallas=0

declare -a portados=()
if [[ -f "$PORTADOS_FILE" ]]; then
    while IFS= read -r linea; do
        linea="${linea%%#*}"
        linea="$(echo "$linea" | xargs)"
        [[ -n "$linea" ]] && portados+=("$linea")
    done < "$PORTADOS_FILE"
fi

es_portado() {
    local f="$1" p
    for p in "${portados[@]+"${portados[@]}"}"; do
        [[ "$f" == "$p" ]] && return 0
    done
    return 1
}

# Imprime "linea:texto" de cada hallazgo de un archivo, una regla por llamada.
hallazgos() {
    local regla="$1" f="$2"
    case "$regla" in
        R1) grep -nF "userdata('user_data')" "$f" ;;
        R2) grep -nF '$this->db' "$f" ;;
        R3) grep -nF 'assetDB' "$f" ;;
        R4) grep -nF 'index.php/' "$f" ;;
        R5)
            grep -noE "load->view\((MAN *\. *)?'[^']+'" "$f" | while IFS= read -r m; do
                local nro="${m%%:*}" vista
                vista="$(echo "$m" | sed -E "s/.*'([^']+)'.*/\1/")"
                [[ -f "views/${vista}.php" ]] || echo "${nro}:vista inexistente views/${vista}.php"
            done
            ;;
    esac
}

declare -A total=([R1]=0 [R2]=0 [R3]=0 [R4]=0 [R5]=0)

while IFS= read -r f; do
    f="${f#./}"
    for regla in R1 R2 R3 R4 R5; do
        salida="$(hallazgos "$regla" "$f")"
        [[ -z "$salida" ]] && continue
        n="$(echo "$salida" | wc -l)"
        total[$regla]=$(( total[$regla] + n ))
        if es_portado "$f"; then
            fallas=$(( fallas + n ))
            echo "$salida" | while IFS= read -r h; do
                echo "::error file=${f},line=${h%%:*}::0b ${regla}: ${h#*:}"
            done
        fi
    done
done < <(find . -name '*.php' -not -path './.git/*' | sort)

echo "Checklist 0b — pendientes en todo el repo:"
echo "  R1 userdata('user_data') : ${total[R1]}"
echo "  R2 \$this->db             : ${total[R2]}"
echo "  R3 assetDB               : ${total[R3]}"
echo "  R4 URLs index.php/       : ${total[R4]}"
echo "  R5 vistas inexistentes   : ${total[R5]}"
echo "Archivos declarados portados: ${#portados[@]} — hallazgos en ellos: ${fallas}"

if (( fallas > 0 )); then
    echo "FALLA: hay archivos portados que no cumplen el checklist 0b."
    exit 1
fi
echo "OK"
