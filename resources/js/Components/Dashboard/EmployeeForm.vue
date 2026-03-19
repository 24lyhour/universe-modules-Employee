<script setup lang="ts">
import { computed, ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible';
import { Users, GraduationCap, Languages, Briefcase, ChevronDown } from 'lucide-vue-next';
import {
    CollapsibleSection,
    FormItemCard,
    FamilyMemberCard,
    FamilySubSection,
    BasicInformationCard,
    EmploymentInformationCard,
    ProfileSidebar,
    CertificationCard,
} from './Widgets/EmployeeFormWidgets';
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
    relationshipTypes: () => [],
    academicLevels: () => [],
    languageProficiencies: () => [],
    employmentTypes: () => [],
});

const emit = defineEmits<{
    schoolChange: [schoolId: number | null];
}>();

const genderOptions = [
    { value: 'male', label: 'Male' },
    { value: 'female', label: 'Female' },
    { value: 'other', label: 'Other' },
];

// ========== FAMILY MEMBERS ==========
const isMarried = computed(() => props.form.marital_status === 'married');
const familySectionOpen = ref(true);
let familyMemberKeyCounter = ref(
    props.form.family_members?.length > 0
        ? Math.max(...props.form.family_members.map((m) => m._key || 0)) + 1
        : 0
);

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

const addFamilyMember = (relationship: FamilyRelationship) => {
    if (!props.form.family_members) props.form.family_members = [];
    props.form.family_members.push(createEmptyFamilyMember(relationship));
    familySectionOpen.value = true;
};

const removeFamilyMember = (index: number) => props.form.family_members.splice(index, 1);

const getFamilyMembersByRelationship = (relationship: FamilyRelationship) =>
    props.form.family_members?.filter((m) => m.relationship === relationship) || [];

const getMemberIndex = (member: FamilyMemberFormData): number =>
    props.form.family_members?.findIndex((m) => m._key === member._key) ?? -1;

const canAddMore = (relationship: FamilyRelationship): boolean =>
    ['child', 'sibling'].includes(relationship) || getFamilyMembersByRelationship(relationship).length === 0;

// ========== ACADEMIC LEVELS ==========
let academicKeyCounter = ref(
    props.form.academic_levels?.length > 0
        ? Math.max(...props.form.academic_levels.map((a) => a._key || 0)) + 1
        : 0
);

const createEmptyAcademicLevel = (): AcademicLevelFormData => ({
    _key: ++academicKeyCounter.value,
    level: null, institution: '', field_of_study: '', degree: '',
    start_date: '', end_date: '', gpa: null, certificate: '', notes: '',
});

const addAcademicLevel = () => {
    if (!props.form.academic_levels) props.form.academic_levels = [];
    props.form.academic_levels.push(createEmptyAcademicLevel());
};

const removeAcademicLevel = (index: number) => props.form.academic_levels.splice(index, 1);

const getAcademicLevelValue = (item: AcademicLevelFormData) => computed({
    get: () => item.level || undefined,
    set: (v: string | undefined) => { item.level = (v as AcademicLevel) || null; },
});

const getGpaValue = (item: AcademicLevelFormData) => computed({
    get: () => item.gpa ?? undefined,
    set: (v: number | undefined) => { item.gpa = v ?? null; },
});

// ========== FOREIGN LANGUAGES ==========
let languageKeyCounter = ref(
    props.form.foreign_languages?.length > 0
        ? Math.max(...props.form.foreign_languages.map((l) => l._key || 0)) + 1
        : 0
);

const createEmptyForeignLanguage = (): ForeignLanguageFormData => ({
    _key: ++languageKeyCounter.value,
    language: '', proficiency: null, certificate: '', certificate_score: '', notes: '',
});

const addForeignLanguage = () => {
    if (!props.form.foreign_languages) props.form.foreign_languages = [];
    props.form.foreign_languages.push(createEmptyForeignLanguage());
};

const removeForeignLanguage = (index: number) => props.form.foreign_languages.splice(index, 1);

const getLanguageProficiency = (item: ForeignLanguageFormData) => computed({
    get: () => item.proficiency || undefined,
    set: (v: string | undefined) => { item.proficiency = (v as LanguageProficiency) || null; },
});

// ========== JOB EXPERIENCES ==========
let experienceKeyCounter = ref(
    props.form.job_experiences?.length > 0
        ? Math.max(...props.form.job_experiences.map((e) => e._key || 0)) + 1
        : 0
);

const createEmptyJobExperience = (): JobExperienceFormData => ({
    _key: ++experienceKeyCounter.value,
    company: '', position: '', employment_type: null, province: '', city: '',
    start_date: '', end_date: '', is_current: false, responsibilities: '',
    achievements: '', reason_for_leaving: '', notes: '',
});

const addJobExperience = () => {
    if (!props.form.job_experiences) props.form.job_experiences = [];
    props.form.job_experiences.push(createEmptyJobExperience());
};

const removeJobExperience = (index: number) => props.form.job_experiences.splice(index, 1);

const getEmploymentType = (item: JobExperienceFormData) => computed({
    get: () => item.employment_type || undefined,
    set: (v: string | undefined) => { item.employment_type = (v as EmploymentType) || null; },
});
</script>

<template>
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Left Column -->
        <div class="space-y-6 lg:col-span-2 lg:max-h-[calc(100vh-10rem)] lg:overflow-y-auto lg:pr-2">
            <BasicInformationCard :form="form" :mode="mode" :marital-statuses="maritalStatuses" />

            <!-- Family Information -->
            <Collapsible v-model:open="familySectionOpen">
                <Card>
                    <CardHeader class="pb-3">
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-2 text-base">
                                <Users class="h-4 w-4" /> Family Information
                            </CardTitle>
                            <CollapsibleTrigger as-child>
                                <Button variant="ghost" size="sm">
                                    <ChevronDown class="h-4 w-4 transition-transform" :class="{ 'rotate-180': familySectionOpen }" />
                                </Button>
                            </CollapsibleTrigger>
                        </div>
                    </CardHeader>
                    <CollapsibleContent>
                        <CardContent class="space-y-6">
                            <FamilySubSection title="Father" add-label="Add Father" :show-add-button="canAddMore('father')" empty-message="No father information added." :is-empty="getFamilyMembersByRelationship('father').length === 0" @add="addFamilyMember('father')">
                                <FamilyMemberCard v-for="m in getFamilyMembersByRelationship('father')" :key="m._key" :member="m" title="Father Details" :gender-options="genderOptions" @remove="removeFamilyMember(getMemberIndex(m))" />
                            </FamilySubSection>
                            <FamilySubSection title="Mother" add-label="Add Mother" :show-add-button="canAddMore('mother')" empty-message="No mother information added." :is-empty="getFamilyMembersByRelationship('mother').length === 0" @add="addFamilyMember('mother')">
                                <FamilyMemberCard v-for="m in getFamilyMembersByRelationship('mother')" :key="m._key" :member="m" title="Mother Details" :gender-options="genderOptions" @remove="removeFamilyMember(getMemberIndex(m))" />
                            </FamilySubSection>
                            <FamilySubSection title="Siblings" add-label="Add Sibling" empty-message="No siblings added yet." :is-empty="getFamilyMembersByRelationship('sibling').length === 0" @add="addFamilyMember('sibling')">
                                <FamilyMemberCard v-for="(m, i) in getFamilyMembersByRelationship('sibling')" :key="m._key" :member="m" title="Sibling" :index="i" show-gender :gender-options="genderOptions" @remove="removeFamilyMember(getMemberIndex(m))" />
                            </FamilySubSection>
                            <FamilySubSection v-if="isMarried" title="Spouse" add-label="Add Spouse" :show-add-button="canAddMore('spouse')" empty-message="" :is-empty="false" has-border-top @add="addFamilyMember('spouse')">
                                <FamilyMemberCard v-for="m in getFamilyMembersByRelationship('spouse')" :key="m._key" :member="m" title="Spouse Details" show-gender show-email show-dependent :gender-options="genderOptions" @remove="removeFamilyMember(getMemberIndex(m))" />
                            </FamilySubSection>
                            <FamilySubSection v-if="isMarried" title="Children" add-label="Add Child" empty-message="No children added yet." :is-empty="getFamilyMembersByRelationship('child').length === 0" @add="addFamilyMember('child')">
                                <FamilyMemberCard v-for="(m, i) in getFamilyMembersByRelationship('child')" :key="m._key" :member="m" title="Child" :index="i" show-gender show-dependent :gender-options="genderOptions" @remove="removeFamilyMember(getMemberIndex(m))" />
                            </FamilySubSection>
                        </CardContent>
                    </CollapsibleContent>
                </Card>
            </Collapsible>

            <!-- Academic Levels -->
            <CollapsibleSection title="Academic Levels" :icon="GraduationCap" show-add add-label="Add" @add="addAcademicLevel">
                <div class="space-y-4">
                    <FormItemCard v-for="(item, idx) in form.academic_levels" :key="item._key" title="Education" :index="idx" @remove="removeAcademicLevel(idx)">
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2"><Label>Level</Label><Select v-model="getAcademicLevelValue(item).value"><SelectTrigger><SelectValue placeholder="Select level" /></SelectTrigger><SelectContent><SelectItem v-for="o in academicLevels" :key="o.value" :value="o.value">{{ o.label }}</SelectItem></SelectContent></Select></div>
                            <div class="space-y-2"><Label>Institution <span class="text-destructive">*</span></Label><Input v-model="item.institution" placeholder="University / School name" /></div>
                            <div class="space-y-2"><Label>Field of Study</Label><Input v-model="item.field_of_study" placeholder="Computer Science" /></div>
                            <div class="space-y-2"><Label>Degree</Label><Input v-model="item.degree" placeholder="Bachelor of Science" /></div>
                            <div class="space-y-2"><Label>Start Date</Label><Input v-model="item.start_date" type="date" /></div>
                            <div class="space-y-2"><Label>End Date</Label><Input v-model="item.end_date" type="date" /></div>
                            <div class="space-y-2"><Label>GPA</Label><Input v-model.number="getGpaValue(item).value" type="number" step="0.01" min="0" max="4" placeholder="3.5" /></div>
                            <div class="space-y-2"><Label>Certificate</Label><Input v-model="item.certificate" placeholder="Certificate name" /></div>
                            <div class="space-y-2"><Label>Notes</Label><Input v-model="item.notes" placeholder="Additional notes" /></div>
                        </div>
                    </FormItemCard>
                    <p v-if="!form.academic_levels?.length" class="text-sm text-muted-foreground">No academic levels added yet.</p>
                </div>
            </CollapsibleSection>

            <!-- Foreign Languages -->
            <CollapsibleSection title="Foreign Languages" :icon="Languages" show-add add-label="Add" @add="addForeignLanguage">
                <div class="space-y-4">
                    <FormItemCard v-for="(item, idx) in form.foreign_languages" :key="item._key" title="Language" :index="idx" @remove="removeForeignLanguage(idx)">
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2"><Label>Language <span class="text-destructive">*</span></Label><Input v-model="item.language" placeholder="English, French, etc." /></div>
                            <div class="space-y-2"><Label>Proficiency</Label><Select v-model="getLanguageProficiency(item).value"><SelectTrigger><SelectValue placeholder="Select proficiency" /></SelectTrigger><SelectContent><SelectItem v-for="o in languageProficiencies" :key="o.value" :value="o.value">{{ o.label }}</SelectItem></SelectContent></Select></div>
                            <div class="space-y-2"><Label>Certificate</Label><Input v-model="item.certificate" placeholder="TOEFL, IELTS, etc." /></div>
                            <div class="space-y-2"><Label>Certificate Score</Label><Input v-model="item.certificate_score" placeholder="Score / Band" /></div>
                            <div class="space-y-2"><Label>Notes</Label><Input v-model="item.notes" placeholder="Additional notes" /></div>
                        </div>
                    </FormItemCard>
                    <p v-if="!form.foreign_languages?.length" class="text-sm text-muted-foreground">No foreign languages added yet.</p>
                </div>
            </CollapsibleSection>

            <!-- Job Experience -->
            <CollapsibleSection title="Job Experience" :icon="Briefcase" show-add add-label="Add" @add="addJobExperience">
                <div class="space-y-4">
                    <FormItemCard v-for="(item, idx) in form.job_experiences" :key="item._key" title="Experience" :index="idx" @remove="removeJobExperience(idx)">
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2"><Label>Company <span class="text-destructive">*</span></Label><Input v-model="item.company" placeholder="Company name" /></div>
                            <div class="space-y-2"><Label>Position</Label><Input v-model="item.position" placeholder="Job title" /></div>
                            <div class="space-y-2"><Label>Employment Type</Label><Select v-model="getEmploymentType(item).value"><SelectTrigger><SelectValue placeholder="Select type" /></SelectTrigger><SelectContent><SelectItem v-for="o in employmentTypes" :key="o.value" :value="o.value">{{ o.label }}</SelectItem></SelectContent></Select></div>
                            <div class="space-y-2"><Label>Province</Label><Input v-model="item.province" placeholder="Province / State" /></div>
                            <div class="space-y-2"><Label>City</Label><Input v-model="item.city" placeholder="City" /></div>
                            <div class="space-y-2"><Label>Start Date</Label><Input v-model="item.start_date" type="date" /></div>
                            <div class="space-y-2"><Label>End Date</Label><Input v-model="item.end_date" type="date" :disabled="item.is_current" /></div>
                            <div class="flex items-center gap-2 pt-6"><Switch v-model="item.is_current" /><Label class="text-sm font-normal">Currently Working</Label></div>
                        </div>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="space-y-2"><Label>Responsibilities</Label><Input v-model="item.responsibilities" placeholder="Main responsibilities" /></div>
                            <div class="space-y-2"><Label>Reason for Leaving</Label><Input v-model="item.reason_for_leaving" placeholder="Reason" :disabled="item.is_current" /></div>
                        </div>
                    </FormItemCard>
                    <p v-if="!form.job_experiences?.length" class="text-sm text-muted-foreground">No job experiences added yet.</p>
                </div>
            </CollapsibleSection>

            <EmploymentInformationCard :form="form" :schools="schools" :departments="departments" :employee-types="employeeTypes" @school-change="emit('schoolChange', $event)" />
            <CertificationCard :form="form" />
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-1 lg:sticky lg:top-6 lg:max-h-[calc(100vh-10rem)] lg:overflow-y-auto lg:self-start">
            <ProfileSidebar :form="form" :mode="mode" />
        </div>
    </div>
</template>
