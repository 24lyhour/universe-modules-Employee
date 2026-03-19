<script setup lang="ts">
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import TiptapEditor from '@/components/TiptapEditor.vue';
import { Switch } from '@/components/ui/switch';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { ImageUpload } from '@/components/shared';
import { Users, GraduationCap, Languages, Briefcase, ChevronDown } from 'lucide-vue-next';
import { CollapsibleSection, FormItemCard, FamilyMemberCard, FamilySubSection } from './Widgets/EmployeeFormWidgets';
import type { InertiaForm } from '@inertiajs/vue3';
import type {
    EmployeeFormData,
    SchoolOption,
    DepartmentOption,
    EmployeeTypeOption,
    MaritalStatusOption,
    FamilyRelationshipOption,
    FamilyMemberFormData,
    FamilyRelationship,
    AcademicLevelOption,
    LanguageProficiencyOption,
    EmploymentTypeOption,
    AcademicLevelFormData,
    ForeignLanguageFormData,
    JobExperienceFormData,
    AcademicLevel,
    LanguageProficiency,
    EmploymentType,
} from '../../types';

interface Props {
    form: InertiaForm<EmployeeFormData>;
    mode?: 'create' | 'edit';
    schools?: SchoolOption[];
    departments?: DepartmentOption[];
    employeeTypes?: EmployeeTypeOption[];
    maritalStatuses?: MaritalStatusOption[];
    relationshipTypes?: FamilyRelationshipOption[];
    academicLevels?: AcademicLevelOption[];
    languageProficiencies?: LanguageProficiencyOption[];
    employmentTypes?: EmploymentTypeOption[];
}

const props = withDefaults(defineProps<Props>(), {
    mode: 'create',
    schools: () => [],
    departments: () => [],
    employeeTypes: () => [],
    maritalStatuses: () => [
        { value: 'single', label: 'Single' },
        { value: 'married', label: 'Married' },
    ],
    relationshipTypes: () => [
        { value: 'spouse', label: 'Spouse' },
        { value: 'child', label: 'Child' },
        { value: 'father', label: 'Father' },
        { value: 'mother', label: 'Mother' },
        { value: 'sibling', label: 'Sibling' },
    ],
    academicLevels: () => [],
    languageProficiencies: () => [],
    employmentTypes: () => [],
});

const emit = defineEmits<{
    schoolChange: [schoolId: number | null];
}>();

// Computed for school select
const selectedSchool = computed({
    get: () => props.form.school_id?.toString(),
    set: (value: string | undefined) => {
        const newValue = value ? parseInt(value) : null;
        props.form.school_id = newValue;
        props.form.department_id = null;
        emit('schoolChange', newValue);
    },
});

// Computed for department select
const selectedDepartment = computed({
    get: () => props.form.department_id?.toString(),
    set: (value: string | undefined) => {
        props.form.department_id = value ? parseInt(value) : null;
    },
});

// Computed for gender select
const selectedGender = computed({
    get: () => props.form.gender || undefined,
    set: (value: string | undefined) => {
        props.form.gender = (value as 'male' | 'female' | 'other') || null;
    },
});

// Computed for employee type select
const selectedEmployeeType = computed({
    get: () => props.form.employee_type || undefined,
    set: (value: string | undefined) => {
        props.form.employee_type = (value as 'full_time' | 'part_time' | 'contract' | 'intern') || null;
    },
});

// Convert avatar_url string to array for ImageUpload component
const avatarImages = computed({
    get: () => props.form.avatar_url ? [props.form.avatar_url] : [],
    set: (value: string[]) => {
        props.form.avatar_url = value.length > 0 ? value[0] : '';
    },
});

// Convert certificate_image string to array for ImageUpload component
const certificateImages = computed({
    get: () => props.form.certificate_image ? [props.form.certificate_image] : [],
    set: (value: string[]) => {
        props.form.certificate_image = value.length > 0 ? value[0] : '';
    },
});

// Computed for salary (convert null to undefined for v-model.number)
const salaryValue = computed({
    get: () => props.form.salary ?? undefined,
    set: (value: number | undefined) => {
        props.form.salary = value ?? null;
    },
});

// Status switch
const isActive = computed({
    get: () => props.form.status,
    set: (value: boolean) => {
        props.form.status = value;
    },
});

// Create account switch
const createAccount = computed({
    get: () => props.form.create_account ?? false,
    set: (value: boolean) => {
        props.form.create_account = value;
        // Clear password fields when disabling account creation
        if (!value) {
            props.form.password = '';
            props.form.password_confirmation = '';
        }
    },
});

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' },
];

// Marital status select
const selectedMaritalStatus = computed({
    get: () => props.form.marital_status || undefined,
    set: (value: string | undefined) => {
        props.form.marital_status = (value as 'single' | 'married') || null;
        // Clear family members when changing to single
        if (value === 'single') {
            props.form.family_members = props.form.family_members.filter(
                (m) => m.relationship !== 'spouse'
            );
        }
    },
});

// Check if marital status is married
const isMarried = computed(() => props.form.marital_status === 'married');

// Family section collapsed state
const familySectionOpen = ref(true);

// Key counter for new family members
let familyMemberKeyCounter = ref(
    props.form.family_members?.length > 0
        ? Math.max(...props.form.family_members.map((m) => m._key || 0)) + 1
        : 0
);

// Create empty family member
const createEmptyFamilyMember = (relationship: FamilyRelationship): FamilyMemberFormData => ({
    _key: ++familyMemberKeyCounter.value,
    relationship,
    name: '',
    gender: null,
    date_of_birth: '',
    age: null,
    occupation: '',
    phone_number: '',
    email: '',
    address: '',
    notes: '',
    is_emergency_contact: false,
    is_dependent: false,
});

// Add family member by relationship type
const addFamilyMember = (relationship: FamilyRelationship) => {
    if (!props.form.family_members) {
        props.form.family_members = [];
    }
    props.form.family_members.push(createEmptyFamilyMember(relationship));
    familySectionOpen.value = true; // Auto-expand when adding
};

// Remove family member
const removeFamilyMember = (index: number) => {
    props.form.family_members.splice(index, 1);
};

// Get family members by relationship
const getFamilyMembersByRelationship = (relationship: FamilyRelationship) => {
    return props.form.family_members?.filter((m) => m.relationship === relationship) || [];
};

// Get index in main array for a specific member
const getMemberIndex = (member: FamilyMemberFormData): number => {
    return props.form.family_members?.findIndex((m) => m._key === member._key) ?? -1;
};

// Check if relationship allows multiple entries
const allowsMultiple = (relationship: FamilyRelationship): boolean => {
    return ['child', 'sibling'].includes(relationship);
};

// Check if can add more of this relationship type
const canAddMore = (relationship: FamilyRelationship): boolean => {
    if (allowsMultiple(relationship)) return true;
    return getFamilyMembersByRelationship(relationship).length === 0;
};

// ========== ACADEMIC LEVELS ==========
let academicKeyCounter = ref(
    props.form.academic_levels?.length > 0
        ? Math.max(...props.form.academic_levels.map((a) => a._key || 0)) + 1
        : 0
);

const createEmptyAcademicLevel = (): AcademicLevelFormData => ({
    _key: ++academicKeyCounter.value,
    level: null,
    institution: '',
    field_of_study: '',
    degree: '',
    start_date: '',
    end_date: '',
    gpa: null,
    certificate: '',
    notes: '',
});

const addAcademicLevel = () => {
    if (!props.form.academic_levels) {
        props.form.academic_levels = [];
    }
    props.form.academic_levels.push(createEmptyAcademicLevel());
};

const removeAcademicLevel = (index: number) => {
    props.form.academic_levels.splice(index, 1);
};

const getAcademicLevelValue = (item: AcademicLevelFormData) => {
    return computed({
        get: () => item.level || undefined,
        set: (value: string | undefined) => {
            item.level = (value as AcademicLevel) || null;
        },
    });
};

const getGpaValue = (item: AcademicLevelFormData) => {
    return computed({
        get: () => item.gpa ?? undefined,
        set: (value: number | undefined) => {
            item.gpa = value ?? null;
        },
    });
};

// ========== FOREIGN LANGUAGES ==========
let languageKeyCounter = ref(
    props.form.foreign_languages?.length > 0
        ? Math.max(...props.form.foreign_languages.map((l) => l._key || 0)) + 1
        : 0
);

const createEmptyForeignLanguage = (): ForeignLanguageFormData => ({
    _key: ++languageKeyCounter.value,
    language: '',
    proficiency: null,
    certificate: '',
    certificate_score: '',
    notes: '',
});

const addForeignLanguage = () => {
    if (!props.form.foreign_languages) {
        props.form.foreign_languages = [];
    }
    props.form.foreign_languages.push(createEmptyForeignLanguage());
};

const removeForeignLanguage = (index: number) => {
    props.form.foreign_languages.splice(index, 1);
};

const getLanguageProficiency = (item: ForeignLanguageFormData) => {
    return computed({
        get: () => item.proficiency || undefined,
        set: (value: string | undefined) => {
            item.proficiency = (value as LanguageProficiency) || null;
        },
    });
};

// ========== JOB EXPERIENCES ==========
let experienceKeyCounter = ref(
    props.form.job_experiences?.length > 0
        ? Math.max(...props.form.job_experiences.map((e) => e._key || 0)) + 1
        : 0
);

const createEmptyJobExperience = (): JobExperienceFormData => ({
    _key: ++experienceKeyCounter.value,
    company: '',
    position: '',
    employment_type: null,
    province: '',
    city: '',
    start_date: '',
    end_date: '',
    is_current: false,
    responsibilities: '',
    achievements: '',
    reason_for_leaving: '',
    notes: '',
});

const addJobExperience = () => {
    if (!props.form.job_experiences) {
        props.form.job_experiences = [];
    }
    props.form.job_experiences.push(createEmptyJobExperience());
};

const removeJobExperience = (index: number) => {
    props.form.job_experiences.splice(index, 1);
};

const getEmploymentType = (item: JobExperienceFormData) => {
    return computed({
        get: () => item.employment_type || undefined,
        set: (value: string | undefined) => {
            item.employment_type = (value as EmploymentType) || null;
        },
    });
};
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column: Form Fields (Scrollable) -->
        <div class="space-y-6 lg:col-span-2 lg:max-h-[calc(100vh-10rem)] lg:overflow-y-auto lg:pr-2">
            <!-- Basic Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Basic Information</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Employee Code -->
                        <div class="space-y-2">
                            <Label for="employee_code">Employee Code <span class="text-destructive">*</span></Label>
                            <Input
                                id="employee_code"
                                v-model="props.form.employee_code"
                                type="text"
                                placeholder="EMP-000001"
                                :disabled="mode === 'edit'"
                            />
                            <p v-if="props.form.errors.employee_code" class="text-xs text-destructive">
                                {{ props.form.errors.employee_code }}
                            </p>
                        </div>

                        <!-- First Name -->
                        <div class="space-y-2">
                            <Label for="first_name">First Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="first_name"
                                v-model="props.form.first_name"
                                type="text"
                                placeholder="John"
                            />
                            <p v-if="props.form.errors.first_name" class="text-xs text-destructive">
                                {{ props.form.errors.first_name }}
                            </p>
                        </div>

                        <!-- Last Name -->
                        <div class="space-y-2">
                            <Label for="last_name">Last Name <span class="text-destructive">*</span></Label>
                            <Input
                                id="last_name"
                                v-model="props.form.last_name"
                                type="text"
                                placeholder="Doe"
                            />
                            <p v-if="props.form.errors.last_name" class="text-xs text-destructive">
                                {{ props.form.errors.last_name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email">Email</Label>
                            <Input
                                id="email"
                                v-model="props.form.email"
                                type="email"
                                placeholder="john.doe@example.com"
                            />
                            <p v-if="props.form.errors.email" class="text-xs text-destructive">
                                {{ props.form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone Number -->
                        <div class="space-y-2">
                            <Label for="phone_number">Phone Number</Label>
                            <Input
                                id="phone_number"
                                v-model="props.form.phone_number"
                                type="text"
                                placeholder="+1 234 567 8900"
                            />
                            <p v-if="props.form.errors.phone_number" class="text-xs text-destructive">
                                {{ props.form.errors.phone_number }}
                            </p>
                        </div>

                        <!-- Gender -->
                        <div class="space-y-2">
                            <Label for="gender">Gender</Label>
                            <Select v-model="selectedGender">
                                <SelectTrigger id="gender">
                                    <SelectValue placeholder="Select gender" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="option in genderOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="props.form.errors.gender" class="text-xs text-destructive">
                                {{ props.form.errors.gender }}
                            </p>
                        </div>

                        <!-- Date of Birth -->
                        <div class="space-y-2">
                            <Label for="date_of_birth">Date of Birth</Label>
                            <Input
                                id="date_of_birth"
                                v-model="props.form.date_of_birth"
                                type="date"
                            />
                            <p v-if="props.form.errors.date_of_birth" class="text-xs text-destructive">
                                {{ props.form.errors.date_of_birth }}
                            </p>
                        </div>

                        <!-- Birth Place -->
                        <div class="space-y-2">
                            <Label for="birth_place">Birth Place</Label>
                            <Input
                                id="birth_place"
                                v-model="props.form.birth_place"
                                type="text"
                                placeholder="City, Country"
                            />
                            <p v-if="props.form.errors.birth_place" class="text-xs text-destructive">
                                {{ props.form.errors.birth_place }}
                            </p>
                        </div>

                        <!-- Marital Status -->
                        <div class="space-y-2">
                            <Label for="marital_status">Marital Status</Label>
                            <Select v-model="selectedMaritalStatus">
                                <SelectTrigger id="marital_status">
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="option in props.maritalStatuses"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="props.form.errors.marital_status" class="text-xs text-destructive">
                                {{ props.form.errors.marital_status }}
                            </p>
                        </div>

                        <!-- Ethnicity -->
                        <div class="space-y-2">
                            <Label for="ethnicity">Ethnicity</Label>
                            <Input
                                id="ethnicity"
                                v-model="props.form.ethnicity"
                                type="text"
                                placeholder="Ethnicity"
                            />
                            <p v-if="props.form.errors.ethnicity" class="text-xs text-destructive">
                                {{ props.form.errors.ethnicity }}
                            </p>
                        </div>
                    </div>

                    <!-- Current Address (Full Width) -->
                    <div class="space-y-2">
                        <Label for="current_address">Current Address</Label>
                        <TiptapEditor
                            v-model="props.form.current_address"
                            placeholder="Street address, City, State, ZIP Code..."
                            min-height="100px"
                            max-height="250px"
                        />
                        <p v-if="props.form.errors.current_address" class="text-xs text-destructive">
                            {{ props.form.errors.current_address }}
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Family Information Card (always visible) -->
            <Collapsible v-model:open="familySectionOpen">
                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Users class="h-4 w-4" />
                                Family Information
                            </CardTitle>
                            <CollapsibleTrigger as-child>
                                <Button variant="ghost" size="sm">
                                    <ChevronDown
                                        class="h-4 w-4 transition-transform"
                                        :class="{ 'rotate-180': familySectionOpen }"
                                    />
                                </Button>
                            </CollapsibleTrigger>
                        </div>
                    </CardHeader>
                    <CollapsibleContent>
                        <CardContent class="space-y-6">
                            <!-- Father Section -->
                            <FamilySubSection
                                title="Father"
                                add-label="Add Father"
                                :show-add-button="canAddMore('father')"
                                empty-message="No father information added."
                                :is-empty="getFamilyMembersByRelationship('father').length === 0"
                                @add="addFamilyMember('father')"
                            >
                                <FamilyMemberCard
                                    v-for="member in getFamilyMembersByRelationship('father')"
                                    :key="member._key"
                                    :member="member"
                                    title="Father Details"
                                    :gender-options="genderOptions"
                                    @remove="removeFamilyMember(getMemberIndex(member))"
                                />
                            </FamilySubSection>

                            <!-- Mother Section -->
                            <FamilySubSection
                                title="Mother"
                                add-label="Add Mother"
                                :show-add-button="canAddMore('mother')"
                                empty-message="No mother information added."
                                :is-empty="getFamilyMembersByRelationship('mother').length === 0"
                                @add="addFamilyMember('mother')"
                            >
                                <FamilyMemberCard
                                    v-for="member in getFamilyMembersByRelationship('mother')"
                                    :key="member._key"
                                    :member="member"
                                    title="Mother Details"
                                    :gender-options="genderOptions"
                                    @remove="removeFamilyMember(getMemberIndex(member))"
                                />
                            </FamilySubSection>

                            <!-- Siblings Section -->
                            <FamilySubSection
                                title="Siblings"
                                add-label="Add Sibling"
                                empty-message="No siblings added yet."
                                :is-empty="getFamilyMembersByRelationship('sibling').length === 0"
                                @add="addFamilyMember('sibling')"
                            >
                                <FamilyMemberCard
                                    v-for="(member, idx) in getFamilyMembersByRelationship('sibling')"
                                    :key="member._key"
                                    :member="member"
                                    title="Sibling"
                                    :index="idx"
                                    show-gender
                                    :gender-options="genderOptions"
                                    @remove="removeFamilyMember(getMemberIndex(member))"
                                />
                            </FamilySubSection>

                            <!-- Spouse Section (only if married) -->
                            <FamilySubSection
                                v-if="isMarried"
                                title="Spouse"
                                add-label="Add Spouse"
                                :show-add-button="canAddMore('spouse')"
                                empty-message=""
                                :is-empty="false"
                                has-border-top
                                @add="addFamilyMember('spouse')"
                            >
                                <FamilyMemberCard
                                    v-for="member in getFamilyMembersByRelationship('spouse')"
                                    :key="member._key"
                                    :member="member"
                                    title="Spouse Details"
                                    show-gender
                                    show-email
                                    show-dependent
                                    :gender-options="genderOptions"
                                    @remove="removeFamilyMember(getMemberIndex(member))"
                                />
                            </FamilySubSection>

                            <!-- Children Section (only if married) -->
                            <FamilySubSection
                                v-if="isMarried"
                                title="Children"
                                add-label="Add Child"
                                empty-message="No children added yet."
                                :is-empty="getFamilyMembersByRelationship('child').length === 0"
                                @add="addFamilyMember('child')"
                            >
                                <FamilyMemberCard
                                    v-for="(member, idx) in getFamilyMembersByRelationship('child')"
                                    :key="member._key"
                                    :member="member"
                                    title="Child"
                                    :index="idx"
                                    show-gender
                                    show-dependent
                                    :gender-options="genderOptions"
                                    @remove="removeFamilyMember(getMemberIndex(member))"
                                />
                            </FamilySubSection>
                        </CardContent>
                    </CollapsibleContent>
                </Card>
            </Collapsible>

            <!-- Academic Levels Section -->
            <CollapsibleSection
                title="Academic Levels"
                :icon="GraduationCap"
                show-add
                add-label="Add"
                @add="addAcademicLevel"
            >
                <div class="space-y-4">
                    <FormItemCard
                        v-for="(item, index) in props.form.academic_levels"
                        :key="item._key"
                        title="Education"
                        :index="index"
                        @remove="removeAcademicLevel(index)"
                    >
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2">
                                <Label>Level</Label>
                                <Select v-model="getAcademicLevelValue(item).value">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select level" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="opt in props.academicLevels"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Institution <span class="text-destructive">*</span></Label>
                                <Input v-model="item.institution" placeholder="University / School name" />
                            </div>
                            <div class="space-y-2">
                                <Label>Field of Study</Label>
                                <Input v-model="item.field_of_study" placeholder="Computer Science" />
                            </div>
                            <div class="space-y-2">
                                <Label>Degree</Label>
                                <Input v-model="item.degree" placeholder="Bachelor of Science" />
                            </div>
                            <div class="space-y-2">
                                <Label>Start Date</Label>
                                <Input v-model="item.start_date" type="date" />
                            </div>
                            <div class="space-y-2">
                                <Label>End Date</Label>
                                <Input v-model="item.end_date" type="date" />
                            </div>
                            <div class="space-y-2">
                                <Label>GPA</Label>
                                <Input v-model.number="getGpaValue(item).value" type="number" step="0.01" min="0" max="4" placeholder="3.5" />
                            </div>
                            <div class="space-y-2">
                                <Label>Certificate</Label>
                                <Input v-model="item.certificate" placeholder="Certificate name" />
                            </div>
                            <div class="space-y-2">
                                <Label>Notes</Label>
                                <Input v-model="item.notes" placeholder="Additional notes" />
                            </div>
                        </div>
                    </FormItemCard>
                    <p v-if="!props.form.academic_levels?.length" class="text-sm text-muted-foreground">
                        No academic levels added yet.
                    </p>
                </div>
            </CollapsibleSection>

            <!-- Foreign Languages Section -->
            <CollapsibleSection
                title="Foreign Languages"
                :icon="Languages"
                show-add
                add-label="Add"
                @add="addForeignLanguage"
            >
                <div class="space-y-4">
                    <FormItemCard
                        v-for="(item, index) in props.form.foreign_languages"
                        :key="item._key"
                        title="Language"
                        :index="index"
                        @remove="removeForeignLanguage(index)"
                    >
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2">
                                <Label>Language <span class="text-destructive">*</span></Label>
                                <Input v-model="item.language" placeholder="English, French, etc." />
                            </div>
                            <div class="space-y-2">
                                <Label>Proficiency</Label>
                                <Select v-model="getLanguageProficiency(item).value">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select proficiency" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="opt in props.languageProficiencies"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Certificate</Label>
                                <Input v-model="item.certificate" placeholder="TOEFL, IELTS, etc." />
                            </div>
                            <div class="space-y-2">
                                <Label>Certificate Score</Label>
                                <Input v-model="item.certificate_score" placeholder="Score / Band" />
                            </div>
                            <div class="space-y-2">
                                <Label>Notes</Label>
                                <Input v-model="item.notes" placeholder="Additional notes" />
                            </div>
                        </div>
                    </FormItemCard>
                    <p v-if="!props.form.foreign_languages?.length" class="text-sm text-muted-foreground">
                        No foreign languages added yet.
                    </p>
                </div>
            </CollapsibleSection>

            <!-- Job Experience Section -->
            <CollapsibleSection
                title="Job Experience"
                :icon="Briefcase"
                show-add
                add-label="Add"
                @add="addJobExperience"
            >
                <div class="space-y-4">
                    <FormItemCard
                        v-for="(item, index) in props.form.job_experiences"
                        :key="item._key"
                        title="Experience"
                        :index="index"
                        @remove="removeJobExperience(index)"
                    >
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2">
                                <Label>Company <span class="text-destructive">*</span></Label>
                                <Input v-model="item.company" placeholder="Company name" />
                            </div>
                            <div class="space-y-2">
                                <Label>Position</Label>
                                <Input v-model="item.position" placeholder="Job title" />
                            </div>
                            <div class="space-y-2">
                                <Label>Employment Type</Label>
                                <Select v-model="getEmploymentType(item).value">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="opt in props.employmentTypes"
                                            :key="opt.value"
                                            :value="opt.value"
                                        >
                                            {{ opt.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Province</Label>
                                <Input v-model="item.province" placeholder="Province / State" />
                            </div>
                            <div class="space-y-2">
                                <Label>City</Label>
                                <Input v-model="item.city" placeholder="City" />
                            </div>
                            <div class="space-y-2">
                                <Label>Start Date</Label>
                                <Input v-model="item.start_date" type="date" />
                            </div>
                            <div class="space-y-2">
                                <Label>End Date</Label>
                                <Input v-model="item.end_date" type="date" :disabled="item.is_current" />
                            </div>
                            <div class="flex items-center gap-2 pt-6">
                                <Switch v-model="item.is_current" />
                                <Label class="text-sm font-normal">Currently Working</Label>
                            </div>
                        </div>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2">
                                <Label>Responsibilities</Label>
                                <Input v-model="item.responsibilities" placeholder="Main responsibilities" />
                            </div>
                            <div class="space-y-2">
                                <Label>Reason for Leaving</Label>
                                <Input v-model="item.reason_for_leaving" placeholder="Reason" :disabled="item.is_current" />
                            </div>
                        </div>
                    </FormItemCard>
                    <p v-if="!props.form.job_experiences?.length" class="text-sm text-muted-foreground">
                        No job experiences added yet.
                    </p>
                </div>
            </CollapsibleSection>

            <!-- Employment Information Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Employment Information</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- School -->
                        <div class="space-y-2">
                            <Label for="school_id">School</Label>
                            <Select v-model="selectedSchool">
                                <SelectTrigger id="school_id">
                                    <SelectValue placeholder="Select school" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="school in props.schools"
                                        :key="school.id"
                                        :value="school.id.toString()"
                                    >
                                        {{ school.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="props.form.errors.school_id" class="text-xs text-destructive">
                                {{ props.form.errors.school_id }}
                            </p>
                        </div>

                        <!-- Department -->
                        <div class="space-y-2">
                            <Label for="department_id">Department</Label>
                            <Select v-model="selectedDepartment" :disabled="!props.form.school_id">
                                <SelectTrigger id="department_id">
                                    <SelectValue placeholder="Select department" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="department in props.departments"
                                        :key="department.id"
                                        :value="department.id.toString()"
                                    >
                                        {{ department.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="props.form.errors.department_id" class="text-xs text-destructive">
                                {{ props.form.errors.department_id }}
                            </p>
                        </div>

                        <!-- Job Title -->
                        <div class="space-y-2">
                            <Label for="job_title">Job Title</Label>
                            <Input
                                id="job_title"
                                v-model="props.form.job_title"
                                type="text"
                                placeholder="Software Engineer"
                            />
                            <p v-if="props.form.errors.job_title" class="text-xs text-destructive">
                                {{ props.form.errors.job_title }}
                            </p>
                        </div>

                        <!-- Employee Type -->
                        <div class="space-y-2">
                            <Label for="employee_type">Employee Type</Label>
                            <Select v-model="selectedEmployeeType">
                                <SelectTrigger id="employee_type">
                                    <SelectValue placeholder="Select type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="type in props.employeeTypes"
                                        :key="type.value"
                                        :value="type.value"
                                    >
                                        {{ type.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="props.form.errors.employee_type" class="text-xs text-destructive">
                                {{ props.form.errors.employee_type }}
                            </p>
                        </div>

                        <!-- Salary -->
                        <div class="space-y-2">
                            <Label for="salary">Salary</Label>
                            <Input
                                id="salary"
                                v-model.number="salaryValue"
                                type="number"
                                min="0"
                                step="0.01"
                                placeholder="0.00"
                            />
                            <p v-if="props.form.errors.salary" class="text-xs text-destructive">
                                {{ props.form.errors.salary }}
                            </p>
                        </div>

                        <!-- Hire Date -->
                        <div class="space-y-2">
                            <Label for="hire_date">Hire Date</Label>
                            <Input
                                id="hire_date"
                                v-model="props.form.hire_date"
                                type="date"
                            />
                            <p v-if="props.form.errors.hire_date" class="text-xs text-destructive">
                                {{ props.form.errors.hire_date }}
                            </p>
                        </div>

                        <!-- Probation Start Date -->
                        <div class="space-y-2">
                            <Label for="probation_date">Probation Start</Label>
                            <Input
                                id="probation_date"
                                v-model="props.form.probation_date"
                                type="date"
                            />
                            <p v-if="props.form.errors.probation_date" class="text-xs text-destructive">
                                {{ props.form.errors.probation_date }}
                            </p>
                        </div>

                        <!-- Probation End Date -->
                        <div class="space-y-2">
                            <Label for="probation_end_date">Probation End</Label>
                            <Input
                                id="probation_end_date"
                                v-model="props.form.probation_end_date"
                                type="date"
                            />
                            <p v-if="props.form.errors.probation_end_date" class="text-xs text-destructive">
                                {{ props.form.errors.probation_end_date }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Certification Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Certification</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <!-- Certificate Name -->
                        <div class="space-y-2">
                            <Label for="certificate">Certificate Name</Label>
                            <Input
                                id="certificate"
                                v-model="props.form.certificate"
                                type="text"
                                placeholder="Bachelor's Degree"
                            />
                            <p v-if="props.form.errors.certificate" class="text-xs text-destructive">
                                {{ props.form.errors.certificate }}
                            </p>
                        </div>

                        <!-- Certificate Code -->
                        <div class="space-y-2">
                            <Label for="certificate_code">Certificate Code</Label>
                            <Input
                                id="certificate_code"
                                v-model="props.form.certificate_code"
                                type="text"
                                placeholder="CERT-2024-001"
                            />
                            <p v-if="props.form.errors.certificate_code" class="text-xs text-destructive">
                                {{ props.form.errors.certificate_code }}
                            </p>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Right Column: Profile Photo, Status & Certificate (Sticky) -->
        <div class="space-y-6 lg:col-span-1 lg:sticky lg:top-6 lg:max-h-[calc(100vh-10rem)] lg:overflow-y-auto lg:self-start">
            <!-- Profile Photo Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Profile Photo</CardTitle>
                </CardHeader>
                <CardContent>
                    <ImageUpload
                        v-model="avatarImages"
                        label=""
                        :multiple="false"
                        :max-files="1"
                        :max-size="5"
                        :error="props.form.errors.avatar_url"
                    />
                </CardContent>
            </Card>

            <!-- Status Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Status</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">Employee Status</p>
                            <p class="text-xs text-muted-foreground">
                                {{ isActive ? 'Employee is active' : 'Employee is inactive' }}
                            </p>
                        </div>
                        <Switch v-model="isActive" />
                    </div>
                </CardContent>
            </Card>

            <!-- Account Settings Card (Only for Create mode) -->
            <Card v-if="mode === 'create'">
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Account Settings</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium">Create Login Account</p>
                            <p class="text-xs text-muted-foreground">
                                Allow employee to log in
                            </p>
                        </div>
                        <Switch v-model="createAccount" />
                    </div>

                    <!-- Password Fields (shown when create_account is true) -->
                    <div v-if="createAccount" class="space-y-4 border-t pt-4">
                        <div class="space-y-2">
                            <Label for="password">Password <span class="text-destructive">*</span></Label>
                            <Input
                                id="password"
                                v-model="props.form.password"
                                type="password"
                                placeholder="Minimum 8 characters"
                                autocomplete="new-password"
                            />
                            <p v-if="props.form.errors.password" class="text-xs text-destructive">
                                {{ props.form.errors.password }}
                            </p>
                        </div>
                        <div class="space-y-2">
                            <Label for="password_confirmation">Confirm Password <span class="text-destructive">*</span></Label>
                            <Input
                                id="password_confirmation"
                                v-model="props.form.password_confirmation"
                                type="password"
                                placeholder="Repeat password"
                                autocomplete="new-password"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Employee will use their email to log in.
                        </p>
                    </div>
                </CardContent>
            </Card>

            <!-- Certificate Image Card -->
            <Card>
                <CardHeader class="pb-3">
                    <CardTitle class="text-base">Certificate Document</CardTitle>
                </CardHeader>
                <CardContent>
                    <ImageUpload
                        v-model="certificateImages"
                        label=""
                        :multiple="false"
                        :max-files="1"
                        :max-size="5"
                        :error="props.form.errors.certificate_image"
                    />
                </CardContent>
            </Card>
        </div>
    </div>
</template>
