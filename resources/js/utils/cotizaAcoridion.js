import { ref, computed, watch } from 'vue';

export function useTrealizados(form) {
    const isFirstPairOpen = ref(false);
    const isSecondPairOpen = ref(false);
    const selectedTrealizados = ref([]);

    const selectedTrealizadosText = computed(() => {
        return selectedTrealizados.value.map(t => `${t.descripcion}`).join('\n');
    });

    const toggleFirstPair = () => {
        isFirstPairOpen.value = !isFirstPairOpen.value;
    };

    const toggleSecondPair = () => {
        isSecondPairOpen.value = !isSecondPairOpen.value;
        if (isSecondPairOpen.value) {
            isFirstPairOpen.value = false;
        }
    };

    const selectTrealizado = (trealizado) => {
        if (!selectedTrealizados.value.some(t => t.id === trealizado.id)) {
            selectedTrealizados.value.push(trealizado);
            isSecondPairOpen.value = true;
        }
    };

    const removeTrealizado = (id) => {
        selectedTrealizados.value = selectedTrealizados.value.filter(t => t.id !== id);
    };

    const clearSelectedTrealizados = () => {
        selectedTrealizados.value = [];
        isSecondPairOpen.value = false;
    };

    watch(selectedTrealizadosText, (newText) => {
        form.tselecionado = newText;
    });

    return {
        isFirstPairOpen,
        isSecondPairOpen,
        selectedTrealizados,
        selectedTrealizadosText,
        toggleFirstPair,
        toggleSecondPair,
        selectTrealizado,
        removeTrealizado,
        clearSelectedTrealizados
    };
}

export function useCselecionados(form) {
    const isFirstPairOpen1 = ref(false);
    const isSecondPairOpen1 = ref(false);
    const selectedCselecionados = ref([]);

    const selectedCselecionadoText = computed(() => {
        return selectedCselecionados.value.map(c => `${c.descripcion}`).join('\n');
    });

    const toggleFirstPair1 = () => {
        isFirstPairOpen1.value = !isFirstPairOpen1.value;
    };

    const toggleSecondPair1 = () => {
        isSecondPairOpen1.value = !isSecondPairOpen1.value;
        if (isSecondPairOpen1.value) {
            isFirstPairOpen1.value = false;
        }
    };

    const selectCselecionado = (cselecionado) => {
        if (!selectedCselecionados.value.some(c => c.id === cselecionado.id)) {
            selectedCselecionados.value.push(cselecionado);
            isSecondPairOpen1.value = true;
        }
    };

    const removeCselecionado = (id) => {
        selectedCselecionados.value = selectedCselecionados.value.filter(c => c.id !== id);
    };

    const clearSelectedCselecionados = () => {
        selectedCselecionados.value = [];
        isSecondPairOpen1.value = false;
    };

    watch(selectedCselecionadoText, (newText) => {
        form.cselecionado = newText;
    });

    return {
        isFirstPairOpen1,
        isSecondPairOpen1,
        selectedCselecionados,
        selectedCselecionadoText,
        toggleFirstPair1,
        toggleSecondPair1,
        selectCselecionado,
        removeCselecionado,
        clearSelectedCselecionados
    };
}

export function useOselecionados(form) {
    const isFirstPairOpen2 = ref(false);
    const isSecondPairOpen2 = ref(false);
    const selectedOselecionados = ref([]);

    const selectedOselecionadoText = computed(() => {
        return selectedOselecionados.value.map(o => `${o.descripcion}`).join('\n');
    });

    const toggleFirstPair2 = () => {
        isFirstPairOpen2.value = !isFirstPairOpen2.value;
    };

    const toggleSecondPair2 = () => {
        isSecondPairOpen2.value = !isSecondPairOpen2.value;
        if (isSecondPairOpen2.value) {
            isFirstPairOpen2.value = false;
        }
    };

    const selectOselecionado = (oselecionado) => {
        if (!selectedOselecionados.value.some(o => o.id === oselecionado.id)) {
            selectedOselecionados.value.push(oselecionado);
            isSecondPairOpen2.value = true;
        }
    };

    const removeOselecionado = (id) => {
        selectedOselecionados.value = selectedOselecionados.value.filter(o => o.id !== id);
    };

    const clearSelectedOselecionado = () => {
        selectedOselecionados.value = [];
        isSecondPairOpen2.value = false;
    };

    watch(selectedOselecionadoText, (newText) => {
        form.oselecionado = newText;
    });

    return {
        isFirstPairOpen2,
        isSecondPairOpen2,
        selectedOselecionados,
        selectedOselecionadoText,
        toggleFirstPair2,
        toggleSecondPair2,
        selectOselecionado,
        removeOselecionado,
        clearSelectedOselecionado
    };
}


export function useDselecionados(form) {
    const isFirstPairOpen3 = ref(false);
    const isSecondPairOpen3 = ref(false);
    const selectedDselecionados = ref([]);

    const selectedDselecionadoText = computed(() => {
        return selectedDselecionados.value.map(d => `${d.descripcion}`).join('\n');
    });

    const toggleFirstPair3 = () => {
        isFirstPairOpen3.value = !isFirstPairOpen3.value;
    };

    const toggleSecondPair3 = () => {
        isSecondPairOpen3.value = !isSecondPairOpen3.value;
        if (isSecondPairOpen3.value) {
            isFirstPairOpen3.value = false;
        }
    };

    const selectDselecionado = (dselecionado) => {
        if (!selectedDselecionados.value.some(d => d.id === dselecionado.id)) {
            selectedDselecionados.value.push(dselecionado);
            isSecondPairOpen3.value = true;
        }
    };

    const removeDselecionado = (id) => {
        selectedDselecionados.value = selectedDselecionados.value.filter(d => d.id !== id);
    };

    const clearSelectedDselecionado = () => {
        selectedDselecionados.value = [];
        isSecondPairOpen3.value = false;
    };

    watch(selectedDselecionadoText, (newText) => {
        form.dselecionado = newText;
    });

    return {
        isFirstPairOpen3,
        isSecondPairOpen3,
        selectedDselecionados,
        selectedDselecionadoText,
        toggleFirstPair3,
        toggleSecondPair3,
        selectDselecionado,
        removeDselecionado,
        clearSelectedDselecionado
    };
}

export function useRselecionados(form) {
    const isFirstPairOpen4 = ref(false);
    const isSecondPairOpen4 = ref(false);
    const selectedRselecionados = ref([]);

    const selectedRselecionadoText = computed(() => {
        return selectedRselecionados.value.map(r => `${r.descripcion}`).join('\n');
    });

    const toggleFirstPair4 = () => {
        isFirstPairOpen4.value = !isFirstPairOpen4.value;
    };

    const toggleSecondPair4 = () => {
        isSecondPairOpen4.value = !isSecondPairOpen4.value;
        if (isSecondPairOpen4.value) {
            isFirstPairOpen4.value = false;
        }
    };

    const selectRselecionado = (rselecionado) => {
        if (!selectedRselecionados.value.some(r => r.id === rselecionado.id)) {
            selectedRselecionados.value.push(rselecionado);
            isSecondPairOpen4.value = true;
        }
    };

    const removeRselecionado = (id) => {
        selectedRselecionados.value = selectedRselecionados.value.filter(r => r.id !== id);
    };

    const clearSelectedRselecionado = () => {
        selectedRselecionados.value = [];
        isSecondPairOpen4.value = false;
    };

    watch(selectedRselecionadoText, (newText) => {
        form.rselecionado = newText;
    });

    return {
        isFirstPairOpen4,
        isSecondPairOpen4,
        selectedRselecionados,
        selectedRselecionadoText,
        toggleFirstPair4,
        toggleSecondPair4,
        selectRselecionado,
        removeRselecionado,
        clearSelectedRselecionado
    };
}
